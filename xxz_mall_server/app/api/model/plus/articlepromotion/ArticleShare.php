<?php

namespace app\api\model\plus\articlepromotion;

use app\common\model\plugin\articlepromotion\ArticleShare as ArticleShareModel;
use app\shop\model\settings\Settings as SettingModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;

/**
 * 推广文章分享记录模型
 */
class ArticleShare extends ArticleShareModel
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
     * 记录分享
     * @param $data
     * @param $user
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function addShareLog($data, $user)
    {
        $userId = isset($data['userId']) && $data['userId'] ? $data['userId'] : $user['user_id'];

        if (!$userId || !$data['shareId'] || $userId == $data['shareId']) return true;

        $model = parent::detail($data['articleId'], $userId, $data['shareId']);

        if ($model) return true;

        //获取配置参数
        $pointsSetting = SettingModel::getItem('exchangepurch');
        $growSetting = SettingModel::getItem('grow');
        $setting = SettingModel::getItem('articlepromotion');
        $isPoints = isset($pointsSetting['is_points']) && $pointsSetting['is_points'] == '1' ? 1 : 0;//是否开启积分
        $isGrow = isset($growSetting['is_grow']) && $growSetting['is_grow'] == '1' && isset($growSetting['is_promote_article']) && $growSetting['is_promote_article'] == '1' ? 1 : 0;//是否开启成长值

        //避免重复
        $article_share_num = Cache::get('article_share_' . $data['articleId'] . '_' . $userId . '_' . $data['shareId']);

        if ($article_share_num) return true;

        Cache::set('article_share_' . $data['articleId'] . '_' . $userId . '_' . $data['shareId'], 1, 10);

        //开始执行
        $this->startTrans();
        try {
            if ($isPoints) {
                $user->setIncPointsByUser($data['shareId'], $setting['forward_award'], '每转发1人发放奖励', 5);
            }

            if ($isGrow) {
                $user->setIncGrowthValueByUser($data['shareId'], $setting['forward_grow'], '每转发1人发放奖励', 4);
            }

            $model = new ArticleShareModel();
            $model->article_id = $data['articleId'];
            $model->user_id = $userId;
            $model->share_id = $data['shareId'];
            $model->points = $isPoints ? $setting['forward_award'] : 0;
            $model->growth_value = $isGrow ? $setting['forward_grow'] : 0;
            $model->app_id = self::$app_id;
            $model->create_time = time();
            $model->save();

            $this->commit();

            Cache::delete('article_share_' . $data['articleId'] . '_' . $userId . '_' . $data['shareId']);

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
