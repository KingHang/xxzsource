<?php

namespace app\api\model\plus\lottery;

use app\common\model\plugin\raffle\Record as RecordModel;
use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Songshenzong\Support\Time;
use think\Paginator;

/**
 * Class GiftPackage
 * 记录模型
 * @package app\common\model\plugin\giftpackage
 */
class Record extends RecordModel
{
    /**
     * 记录列表
     * @param $data
     * @param $user
     * @return Paginator
     * @throws DbException
     */
    public function getList($data, $user)
    {
        $model = $this;
        return $model->alias('r')
            ->where('user_id', '=', $user['user_id'])
            ->field('r.*')
            ->order('r.create_time', 'desc')
            ->paginate($data);
    }

    /**
     * 记录列表
     * @param $limit
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getLimitList($limit)
    {
        $model = $this;
        $list =  $model->alias('r')
            ->with(['user'])
            ->field('r.*')
            ->order('r.create_time', 'desc')
            ->limit($limit)
            ->select();

        if ($list) {
            foreach ($list as &$value) {
                $value['showText'] = Time::formatTime($value['create_time']) . $value['user']['nickName'] . '抽中了' . $value['record_name'];
            }
        }

        return $list;
    }
}
