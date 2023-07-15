<?php

namespace app\shop\controller\authority;

use app\common\model\app\App;
use app\shop\model\shop\Access as AccessModel;
use app\common\model\settings\Settings as SettingModel;
use app\shop\controller\Controller;
use app\shop\model\auth\User as UserModel;
use app\shop\model\auth\Role;
use app\shop\model\auth\User as AuthUserModel;
use Exception;

/**
 * 管理员
 */
class User extends Controller
{
    /**
     * 首页列表
     * @return \think\response\Json
     */
    public function index()
    {
        $model = new UserModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增信息
     * @return \think\response\Json
     */
    public function addInfo()
    {
        $model = new Role();
        // 角色列表
        $roleList = $model->getTreeData();
        return $this->renderSuccess('', compact('roleList'));
    }

    /**
     * 新增
     * @return \think\response\Json
     */
    public function add()
    {
        $data = $this->postData();
        $model = new UserModel();
        $num = $model->getUserName(['user_name' => $data['user_name']]);
        if ($num > 0) {
            return $this->renderError('用户名已存在');
        }
        if (!isset($data['role_id'])) {
            return $this->renderError('请选择所属角色');
        }
        if ($data['confirm_password'] != $data['password']) {
            return $this->renderError('确认密码和登录密码不一致');
        }
        $model = new UserModel();
        if ($model->add($data)) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError('添加失败');
    }

    /**
     * 修改信息
     * @param $shop_user_id
     * @return \think\response\Json
     */
    public function editInfo($shop_user_id)
    {
        $info = UserModel::detail(['shop_user_id' => $shop_user_id], ['UserRole']);

        $role_arr = array_column($info->toArray()['UserRole'], 'role_id');

        $model = new Role();
        // 角色列表
        $roleList = $model->getTreeData();
        return $this->renderSuccess('', compact('info', 'roleList', 'role_arr'));
    }

    /**
     * 编辑
     * @param $shop_user_id
     * @return \think\response\Json
     */
    public function edit($shop_user_id)
    {
        $data = $this->postData();
        if($this->request->isGet()){
            return $this->editInfo($shop_user_id);
        }

        $model = new UserModel();
        $num = $model->getUserName(['user_name' => $data['user_name']], $data['shop_user_id']);
        if ($num > 0) {
            return $this->renderError('用户名已存在');
        }
        if (!isset($data['access_id'])) {
            return $this->renderError('请选择所属角色');
        }
        if (isset($data['password']) && !empty($data['password'])) {
            if (!isset($data['confirm_password'])) {
                return $this->renderError('请输入确认密码');
            } else {
                if ($data['confirm_password'] != $data['password']) {
                    return $this->renderError('确认密码和登录密码不一致');
                }
            }
        }
        if (empty($data['password'])) {
            if (isset($data['confirm_password']) && !empty($data['confirm_password'])) {
                return $this->renderError('请输入登录密码');
            }
        }

        // 更新记录
        if ($model->edit($data, ['shop_user_id' => $data['shop_user_id']])) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError()?:'更新失败');


    }

    /**
     * 删除
     */
    public function delete($shop_user_id)
    {
        $model = new UserModel();
        if ($model->del(['shop_user_id' => $shop_user_id])) {
            return $this->renderSuccess('删除成功');
        }
        return $this->renderError('删除失败');
    }

    /**
     * 获取角色菜单信息
     */
    public function getRoleList()
    {
        $user = $this->store['user'];
        $user_info = AuthUserModel::find($user['shop_user_id']);
        $is_need = 0;
        if ($user_info['is_super'] == 1) {
            $roleIds = App::detail($user_info['app_id']);
            $model = new AccessModel();
            if ($roleIds && $roleIds->trade_id) {
                $menus = $model->getListByUser($user['shop_user_id'], '0', $roleIds->trade_id);
                $is_need = 1;
            } else {
                $menus = $model->getList();
            }
        } else {
            $model = new AccessModel();
            $menus = $model->getListByUser($user['shop_user_id']);
            $is_need = 1;
        }

        if ($is_need == 1) {
            foreach ($menus as $key => $val) {
                if ($val['redirect_name'] != $val['children'][0]['path']) {
                    $menus[$key]['redirect_name'] = $menus[$key]['children'][0]['path'];
                }
            }
        }

        return $this->renderSuccess('', compact('menus'));
    }

    /**
     * 获取用户信息
     * @throws Exception
     */
    public function getUserInfo()
    {
        $store = session('xxzmall_store');
        $user = [];
        if (!empty($store)) {
            $user = $store['user'];
        }
        $setting = SettingModel::getItem('store');
        // 商城名称
        $shop_name = isset($setting['name']) ? $setting['name'] : '';
        // 商城logo
        $shop_logo = isset($setting['logo']) ? $setting['logo'] : '';
        // 当前系统版本
        $version = get_version();
        // 判断是否已选择行业
        $isTrade = App::checkTrade($user);
        return $this->renderSuccess('', compact('user', 'shop_name', 'shop_logo', 'version', 'isTrade'));
    }
}