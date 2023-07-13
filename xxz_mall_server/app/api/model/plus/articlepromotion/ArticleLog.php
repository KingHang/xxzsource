<?php

namespace app\api\model\plus\articlepromotion;

use app\common\exception\BaseException;
use app\common\model\plugin\articlepromotion\ArticleLog as ArticleLogModel;
use app\shop\model\settings\Settings as SettingModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use think\Model;

/**
 * 推广文章浏览记录模型
 */
class ArticleLog extends ArticleLogModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

    /**
     * 统计时长
     * @param $data
     * @param $user
     * @return array|bool|Model
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function recordDuration($data, $user)
    {
        $userId = isset($data['userId']) && $data['userId'] ? $data['userId'] : $user['user_id'];

        if (!$userId) return true;

        $model = parent::detail($data['articleId'], $userId);

        //获取配置参数
        $pointsSetting = SettingModel::getItem('exchangepurch');
        $growSetting = SettingModel::getItem('grow');
        $setting = SettingModel::getItem('articlepromotion');
        $isPoints = isset($pointsSetting['is_points']) && $pointsSetting['is_points'] == '1' ? 1 : 0;//是否开启积分
        $isGrow = isset($growSetting['is_grow']) && $growSetting['is_grow'] == '1' && isset($growSetting['is_promote_article']) && $growSetting['is_promote_article'] == '1' ? 1 : 0;//是否开启成长值

        $times = isset($data['times']) ? $data['times'] : 0;
        $isReward = 0;
        $points = 0;
        $growth_value = 0;

        //避免重复
        $article_view_num = Cache::get('article_view_' . $data['articleId'] . '_' . $userId);

        if ($article_view_num) return true;

        Cache::set('article_view_' . $data['articleId'] . '_' . $userId, 1, 10);

        //开始执行
        $this->startTrans();
        try {
            if ($model) {
                $times = $model->times + $times;
                $isReward = $model->is_reward;
                $points = $model->points;
                $growth_value = $model->growth_value;
            }

            //判断有没有发放奖励
            if (!$isReward && $times >= $setting['read_time']) {
                $isReward = 1;
                if ($isPoints) {
                    $user->setIncPoints($setting['read_award'], '满足阅读时长发放奖励', 4);
                    $points = $setting['read_award'];
                }
                if ($isGrow) {
                    $user->setIncGrowthValue($setting['read_grow'], '满足阅读时长发放奖励', 3);
                    $growth_value = $setting['read_grow'];
                }
                //累计发放
                $this->articleAsc($data['articleId'], $points, $growth_value);
            }

            //更新
            if ($model) {
                $model->read = $model->read + 1;
                $model->times = $times;
                $model->is_reward = $isReward;
                $model->points = $points;
                $model->growth_value = $growth_value;
                $model->update_time = time();
                $model->save();
            } else {
                $model = new ArticleLogModel();
                $model->article_id = $data['articleId'];
                $model->user_id = $userId;
                $model->read = 1;
                $model->times = $times;
                $model->is_reward = $isReward;
                $model->points = $points;
                $model->growth_value = $growth_value;
                $model->app_id = self::$app_id;
                $model->create_time = time();
                $model->save();
            }

            $this->commit();

            Cache::delete('article_view_' . $data['articleId'] . '_' . $userId);

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 文章累计发放奖励
     * @param $article_id
     * @param $points
     * @param $growth_value
     * @return bool
     * @throws BaseException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private function articleAsc($article_id, $points, $growth_value)
    {
        if (!$model = Article::detail($article_id)) {
            throw new BaseException(['msg' => '文章不存在']);
        }
        // 累积发放奖励
        $model->where('article_id', '=', $article_id)->inc('exchangepurch', $points)->update();
        $model->where('article_id', '=', $article_id)->inc('growth_value', $growth_value)->update();
        return true;
    }
}
