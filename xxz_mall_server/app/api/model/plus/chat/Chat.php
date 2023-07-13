<?php

namespace app\api\model\plus\chat;

use app\common\model\plugin\chat\Chat as ChatModel;
use app\api\model\supplier\Purveyor as SupplierModel;
use app\api\model\user\User as UserModel;
use app\api\model\settings\Settings as SettingModel;

/**
 * 客服消息模型类
 */
class Chat extends ChatModel
{

    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'identify',
        'status',
        'update_time'
    ];

    //消息列表
    public function myList($user)
    {
        $ChatRelation = new ChatRelation();
        $list = $ChatRelation->with(['user', 'supplier.logo'])
            ->where(['user_id' => $user['user_id']])
            ->order('update_time desc')
            ->select();
        foreach ($list as $key => &$value) {
            $where['supplier_user_id'] = $value['supplier_user_id'];
            $where['user_id'] = $user['user_id'];
            $where['status'] = 0;
            $value['num'] = $this->where($where)->count();
            unset($where['status']);
            $value['newMessage'] = $this->where($where)->order('chat_id desc')->field('content,create_time,type')->find();
        }
        return $list;
    }

    //消息列表
    public function mySupplierList($supplier_user_id)
    {
        $ChatRelation = new ChatRelation();
        $list = $ChatRelation->with(['user', 'supplier.logo'])
            ->where(['supplier_user_id' => $supplier_user_id])
            ->order('update_time desc')
            ->select();
        foreach ($list as $key => &$value) {
            $where['supplier_user_id'] = $supplier_user_id;
            $where['user_id'] = $value['user_id'];
            $where['status'] = 0;
            $value['num'] = $this->where($where)->count();
            unset($where['status']);
            $value['newMessage'] = $this->where($where)->order('chat_id desc')->field('content,create_time,type')->find();
        }
        return $list;
    }

    //获取聊天信息
    public function getMessage($data, $user)
    {
        $where['supplier_user_id'] = $data['supplier_user_id'];
        $where['user_id'] = $user['user_id'];
        $list = $this->where($where)
            ->with(['user', 'supplier.logo'])
            ->order('chat_id desc')
            ->paginate($data);
        $this->where($where)->update(['status' => 1]);
        return $list;
    }

    //获取消息条数
    public function mCount($user)
    {
        $num = 0;
        if ($user) {
            $where[] = ['user_id', '=', $user['user_id']];
            $where[] = ['status', '=', 0];
            $where[] = ['msg_type', '=', 1];
            $num = $this->where($where)->count();
        }
        return $num;
    }

    //获取用户信息
    public function getInfo($data)
    {
        $userInfo = UserModel::detail($data['user_id']);
        $supplierInfo = SupplierModel::detail($data['shop_supplier_id'], ['logo']);
        $data['avatarUrl'] = $userInfo['avatarUrl'];
        $data['logo'] = $supplierInfo['logo']['file_path'];
        $data['url'] = SettingModel::getSysConfig()['url'];
        return $data;
    }

    public static function getNoReadCount($supplier_user_id){
        return self::where('supplier_user_id', '=', $supplier_user_id)
            ->where('status', '=', 0)
            ->where('msg_type', '=', 2)
            ->count();
    }

}
