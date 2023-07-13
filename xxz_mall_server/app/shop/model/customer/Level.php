<?php

namespace app\shop\model\customer;

use app\common\model\customer\Level as LevelModel;
use app\shop\model\customer\User as UserModel;

/**
 * 用户会员等级模型
 */
class Level extends LevelModel
{
    /**
     * 获取列表记录
     */
    public function getList($data)
    {
        return $this->where('is_delete', '=', 0)
            ->order(['level' => 'asc', 'create_time' => 'asc'])
            ->paginate($data, false, [
                'query' => request()->request()
            ]);
    }

    /**
     * 获取列表记录
     */
    public function getLists()
    {
        return $this->field('level_id,level_name')
            ->order(['level' => 'asc', 'create_time' => 'asc'])
            ->select();
    }


    /**
     * 新增记录
     */
    public function add($data)
    {
        $data['level']['app_id'] = self::$app_id;
//        $data['remark'] = $this->setRemark($data);
        return $this->save($data['level']);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        return $this->save($data['level']);
    }

    private function setRemark($data){
        $remark = '';
        if($data['open_money'] == 1){
            $money = sprintf('%.2f',$data['upgrade_money']);
            $remark .= "会员消费满{$money}元可升级到此等级";
        }
        if($data['open_points'] == 1){
            if(!empty($remark)){
                $remark .= '\r\n';
            }
            $remark .= "会员积分满{$data['upgrade_points']}可升级到此等级";
        }
        if($data['open_invite'] == 1){
            if(!empty($remark)){
                $remark .= '\r\n';
            }
            $remark .= "会员邀请人数满{$data['upgrade_invite']}可升级到此等级";
        }
        return $remark;
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        // 判断该等级下是否存在会员
        if (UserModel::checkExistByGradeId($this['level_id'])) {
            return false;
        }
        return $this->save(['is_delete' => 1]);
    }

}