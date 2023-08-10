<?php

namespace app\common\model\app;

use app\common\exception\BaseException;
use think\facade\Cache;
use app\common\model\BaseModel;
use think\model\relation\BelongsTo;

/**
 * 应用模型
 */
class App extends BaseModel
{
    protected $name = 'app';

    protected $pk = 'app_id';

    /**
     * 关联行业
     * @return BelongsTo
     */
    public function trade()
    {
        return $this->belongsTo('app\common\model\super\Trade', 'trade_id', 'trade_id');
    }

    /**
     * 获取应用信息
     */
    public static function detail($app_id)
    {
        return (new static())->with('trade')->find($app_id);
    }

    /**
     * 从缓存中获取app信息
     */
    public static function getAppCache($app_id = null)
    {
        if (is_null($app_id)) {
            $self = new static();
            $app_id = $self::$app_id;
        }
        if (!$data = Cache::get('app_' . $app_id)) {
            $data = self::detail($app_id);
            if (empty($data)) throw new BaseException(['msg' => '未找到当前应用信息']);
            Cache::tag('cache')->set('app_' . $app_id, $data);
        }
        return $data;
    }

    /**
     * 启用商城
     * @return bool
     */
    public function updateStatus()
    {
        return $this->save([
            'status' => !$this['status'],
        ]);
    }

    /**
     * 所有商城
     */
    public static function getAll()
    {
        return (new self())->where('is_delete', '=', 0)
            ->where('is_recycle', '=', 0)
            ->select();
    }

    /**
     * 判断是否选择需要强制选择行业
     * @param $user
     * @return int
     */
    public static function checkTrade($user)
    {
        if (!empty($user) && isset($user['app_id']) && $user['app_id']) {
            // 主商城不需要行业限制
            if ($user['app_id'] == '10001') return 1;

            $hasTrade = self::detail($user['app_id']);

            if ($hasTrade && $hasTrade->trade_id) return 1;
        }

        return 0;
    }
}
