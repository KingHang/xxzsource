<?php

namespace app\api\model\plus\articlepromotion;

use app\common\exception\BaseException;
use app\common\model\plugin\articlepromotion\Article as ArticleModel;
use app\common\model\plugin\articlepromotion\ArticleComment;
use app\common\model\plugin\articlepromotion\ArticleLog;
use app\common\model\user\User;
use app\shop\model\settings\Settings as SettingModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use think\Paginator;

/**
 * 推广文章模型
 */
class Article extends ArticleModel
{
    /**
     * 追加字段
     */
    protected $append = [
        'view_time'
    ];

    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'update_time'
    ];

    /**
     * 文章详情：HTML实体转换回普通字符
     * @param $value
     * @return string
     */
    public function getArticleContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    public function getViewTimeAttr($value, $data)
    {
        return $data['virtual_views'] + $data['actual_views'];
    }

    /**
     * 文章详情
     * @param $article_id
     * @return array|Model|null
     * @throws BaseException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function detail($article_id)
    {
        if (!$model = parent::detail($article_id)) {
            throw new BaseException(['msg' => '文章不存在']);
        }
        // 累积阅读数
        $model->where('article_id', '=', $article_id)->inc('actual_views', 1)->update();
        return $model;
    }

    /**
     * 获取文章列表
     * @param $params
     * @param int $category_id
     * @return Paginator
     * @throws DbException
     */
    public function getList($params, $category_id = 0)
    {
        $model = $this;
        $category_id > 0 && $model = $model->where('category_id', '=', $category_id);
        return $model ->with(['image', 'category'])
            ->where('article_status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['article_sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params);
    }
    public function addIslike($data,$user)
    {
        if (!$model = parent::detail($data['article_id'])) {
            throw new BaseException(['msg' => '文章不存在']);
        }
        $data['user_id'] = $user['user_id'];
        $log = ArticleLog::where(['user_id'=>$user['user_id'],'article_id'=>$data['article_id']])->find();

        if ($log){
            if($log->is_like ==1)
            {
                $model->where('article_id', '=', $data['article_id'])->dec('like_num', 1)->update();
                return $log->save(['is_like'=>0]);
            }
            if($log->save(['is_like'=>1])){
                // 累积收藏量
                return $model->where('article_id', '=', $data['article_id'])->inc('like_num', 1)->update();
            }
        }else{
             (new ArticleLog())->save($data);
            return $model->where('article_id', '=', $data['article_id'])->inc('like_num', 1)->update();
        }
    }
    public function addCollect($data,$user)
    {
        if (!$model = parent::detail($data['article_id'])) {
            throw new BaseException(['msg' => '文章不存在']);
        }
        $data['user_id'] = $user['user_id'];
        $log = ArticleLog::where(['user_id'=>$user['user_id'],'article_id'=>$data['article_id']])->find();
        if ($log){
            //如果有记录并且已经收藏
            if ($log->is_collect ==1){//取消收藏
                //减少收藏次数,将收藏状态改为为收藏
                $model->where('article_id', '=', $data['article_id'])->dec('collect_num', 1)->update();
                return $log->save(['is_collect'=>0]);
            }
            if($result = $log->save(['is_collect'=>1])){
                // 累积收藏量
                return $model->where('article_id', '=', $data['article_id'])->inc('collect_num', 1)->update();
            }
        }else{
             (new ArticleLog())->save($data);
            return $model->where('article_id', '=', $data['article_id'])->inc('collect_num', 1)->update();
        }
    }
    public function addComment($data,$user)
    {
        if (!$model = parent::detail($data['article_id'])) {
            throw new BaseException(['msg' => '文章不存在']);
        }
        $setting = SettingModel::getItem('articlepromotion');
        // 验证appid和appsecret是否填写
        if (empty($setting['app_id']) || empty($setting['app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-插件-文章推广-设置] 填写appid和appsecret']);
        }
        $data['app_id'] = $setting['app_id'];
        $data['secret'] = $setting['app_secret'];
        if (User::checkMsg($data)['errcode']==0){
            $data['user_id'] = $user['user_id'];
            $data['app_id'] = self::$app_id;
            return (new ArticleComment())->save($data);
        }else{
            return $this->error=User::checkMsg($data)['errmsg'];
        }

    }
}
