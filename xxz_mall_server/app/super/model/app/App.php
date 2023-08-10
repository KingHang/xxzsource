<?php

namespace app\super\model\app;

use app\super\model\page\Home as PageModel;
use app\super\model\Shop as ShopUser;
use app\common\model\super\TradeAccess;
use app\common\model\app\App as AppModel;
use app\super\model\user\Grade as GradeModel;
use app\mall\model\auth\Role as RoleModel;
use app\mall\model\mall\Access as AccessModel;
use app\mall\model\purveyor\Purveyor;

class App extends AppModel
{
    /**
     * 获取小程序列表
     */
    public function getList($limit, $is_recycle = false)
    {
        return $this->alias('app')->with('trade')->field(['app.*,user.user_name'])->where('is_recycle', '=', (int)$is_recycle)
            ->join('mall_user user', 'user.app_id = app.app_id','left')
            ->where('user.is_super', '=', 1)
            ->where('app.is_delete', '=', 0)
            ->order(['create_time' => 'asc'])
            ->paginate($limit);
    }

    /**
     * 新增记录
     */
    public function add($data)
    {
        if ($data['password'] !== $data['password_confirm']) {
            $this->error = '确认密码不正确';
            return false;
        }
        if (!isset($data['trade_id']) && !empty($data['trade_id'])) {
            $this->error = '请选择行业';
            return false;
        }
        if (ShopUser::checkExist($data['user_name'])) {
            $this->error = '商家用户名已存在';
            return false;
        }
        $this->startTrans();
        try {
            // 添加小程序记录
            $this->save($data);
            // 新增商家用户信息
            $ShopUser = new ShopUser;
            if (!$ShopUser->add($this['app_id'], $data)) {
                $this->error = $ShopUser->error;
                return false;
            }
            // 新增自营店铺
            $supplier_data['purveyor'] = [
                'name' => $data['app_name'],
                'real_name' => '',
                'store_type' => 20,
                'user_name' => $data['user_name'],
                'password' => $data['password'],
                'confirm_password' => $data['password_confirm'],
                'app_id' => $this['app_id'],
                'user_id' => 0
            ];
            (new Purveyor())->add($supplier_data,1);
            // 新增应用diy配置
            (new PageModel)->insertDefault($this['app_id']);
            // 默认等级
            (new GradeModel)->insertDefault($this['app_id']);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 修改记录
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            $save_data = [
                'app_name' => $data['app_name'],
                'trade_id' => $data['trade_id']
            ];
            $this->save($save_data);
            $user_data = [
                'user_name' => $data['user_name']
            ];
            if (!empty($data['password'])) {
                $user_data['password'] = salt_hash($data['password']);
            }
            $shop_user = (new ShopUser())->where('app_id', '=', $this['app_id'])->where('is_super', '=', 1)->find();
            if($shop_user['user_name'] != $data['user_name']){
                if (ShopUser::checkExist($data['user_name'])) {
                    $this->error = '商家用户名已存在';
                    return false;
                }
            }
            $shop_user->save($user_data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
    /**
     * 移入移出回收站
     */
    public function recycle($is_recycle = true)
    {
        return $this->save(['is_recycle' => (int)$is_recycle]);
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }


}