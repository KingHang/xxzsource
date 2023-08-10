<?php

namespace app\mall\model\mall;

use app\common\model\mall\LoginLog as LoginLogModel;
use app\common\model\mall\User as UserModel;
use app\mall\model\mall\Sms as SmsModel;
use app\mall\model\app\App;
use app\super\model\user\Grade as GradeModel;
use app\super\model\page\Home as PageModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use app\mall\model\purveyor\Purveyor;

/**
 * 后台管理员登录模型
 */
class User extends UserModel
{
    /**
     *检查登录
     */
    public function checkLogin($user)
    {
        $where['user_name'] = $user['username'];
        $where['password'] = $user['password'];
        $where['is_delete'] = 0;

        if (!$user = $this->where($where)->with(['app'])->find()) {
            return false;
        }
        if (empty($user['app'])) {
            $this->error = '登录失败, 未找到应用信息';
            return false;
        }
        if ($user['app']['is_delete']) {
            $this->error = '登录失败, 当前应用已删除';
            return false;
        }
        // 保存登录状态
        $this->loginState($user);
        // 写入登录日志
        LoginLogModel::add($where['user_name'], \request()->ip(), '登录成功');
        return true;
    }

    /**
     * 修改密码
     */
    public function editPass($data, $user)
    {
        $user_info = User::detail($user['mall_user_id']);
        if ($data['password'] != $data['confirmPass']) {
            $this->error = '密码错误';
            return false;
        }
        if ($user_info['password'] != salt_hash($data['oldpass'])) {
            $this->error = '两次密码不相同';
            return false;
        }
        $date['password'] = salt_hash($data['password']);
        $user_info->save($date);
        return true;
    }

    /**
     * 根据验证码修改密码
     * @param $data
     * @param $user
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function editPassByCode($data, $user)
    {
        $user_info = User::detail($user['mall_user_id']);

        if (!$user_info) return false;

        if (!$this->check($data, 3)) return false;

        if ($data['password'] != $data['confirmPass']) {
            $this->error = '两次密码不相同';
            return false;
        }

        $updateData['password'] = salt_hash($data['password']);

        $user_info->save($updateData);

        return true;
    }

    /**
     * 短信验证码用户登录
     */
    public function smsLogin($data)
    {
        $where = [];
        $where['mobile'] = $data['mobile'];
        $where['is_delete'] = 0;

        if (!$user = $this->where($where)->with(['app'])->find()) {
            $this->error = '登录失败, 无当前手机号';
            return false;
        }

        if (empty($user['app'])) {
            $this->error = '登录失败, 未找到应用信息';
            return false;
        }

        if ($user['app']['is_delete']) {
            $this->error = '登录失败, 当前应用已删除';
            return false;
        }

        if (!$this->check($data, 1)) {
            return false;
        }

        // 保存登录状态
        $this->loginState($user);
        // 写入登录日志
        LoginLogModel::add($where['mobile'], \request()->ip(), '登录成功');
        return true;
    }

    /**
     * 重置密码
     */
    public function resetPassword($data)
    {
        $user = $this->where('mobile', '=', $data['mobile'])->find();
        if ($user) {
             if (!$this->check($data, 3)) {
                return false;
            }

            $user->password = salt_hash($data['password']);
            return ($user->save());
        } else {
            $this->error = '手机号不存在';
            return false;
        }
    }

    /**
     * 注册
     */
    public function smsRegister($data)
    {
        if (self::checkExist($data['mobile'])) {
            $this->error = '商家用户名已存在';
            return false;
        }

        $user = $this->where('mobile', '=', $data['mobile'])->find();
        if ($user) {
            $this->error = '手机号码已存在';
            return false;
        }

        if (!$this->check($data, 2)) {
            return false;
        }

        $this->startTrans();

        try {
            // 添加小程序记录
            $app = new App;
            $arr = [];
            $arr['app_name'] = $data['mobile'];
            $app->save($arr);
            $arr_user = [];
            $arr_user['app_id'] = $app['app_id'];
            $this->save([
                'user_name' => $data['mobile'],
                'mobile' => $data['mobile'],
                'real_name' => $data['mobile'],
                'app_id' => $app['app_id'],
                'is_super' => 1,
            ]);
            // 新增自营店铺
            $supplier_data['purveyor'] = [
                'name' => $data['mobile'],
                'real_name' => '',
                'store_type' => 20,
                'user_name' => $data['mobile'],
                'app_id' => $app['app_id'],
                'user_id' => 0,
                'password' => '',
                'confirm_password' => '',
            ];
            (new Purveyor())->add($supplier_data,1);
            // 新增应用diy配置
            (new PageModel)->insertDefault($this['app_id']);

            // 默认等级
            (new GradeModel)->insertDefault($this['app_id']);

            $this->commit();
            
            // 保存登录状态
            $this->loginState($this);

            // 写入登录日志
            LoginLogModel::add($this['mobile'], \request()->ip(), '登录成功');
            
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 验证
     * @param $data
     * @param int $type 1:登录,2:注册,3:重置密码,默认 1
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private function check($data, $type = 1)
    {
        $sence = '';
        if ($type == 2) {
            $sence = 'smsregister';
        } elseif ($type == 3) {
            $sence = 'smsresetpassword';
        } elseif ($type == 1) {
            $sence = 'smslogin';
        } else {
            $this->error = '参数错误';
            return false;
        }
        // 判断验证码是否过期、是否正确
        $sms_model = new SmsModel();
        $sms_record_list = $sms_model->withoutGlobalScope()
            ->where('mobile', '=', $data['mobile'])
            ->where('sence', '=', $sence)
            ->order(['create_time' => 'desc'])
            ->limit(1)->select();

        if (count($sms_record_list) == 0) {
            $this->error = '未查到短信发送记录';
            return false;
        }

        $sms_model = $sms_record_list[0];

        if ((time() - strtotime($sms_model['create_time'])) / 60 > 30) {
            $this->error = '短信验证码超时';
            return false;
        }

        if ($sms_model['code'] != $data['code']) {
            $this->error = '验证码不正确';
            return false;
        }

        return true;
    }
}
