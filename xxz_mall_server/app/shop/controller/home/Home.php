<?php

namespace app\shop\controller\home;

use app\common\enum\settings\SettingEnum;
use app\shop\controller\Controller;
use app\shop\model\page\Home as PageModel;
use app\shop\model\page\HomeCategory as PageCategoryModel;
use app\shop\model\settings\Settings as SettingModel;
/**
 * App页面管理
 */
class Home extends Controller
{
    /**
     * 页面列表
     */
    public function index()
    {
        $model = new PageModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增页面
     */
    public function add()
    {
        $model = new PageModel;
        if ($this->request->isGet()) {
            return $this->renderSuccess('', [
                'defaultData' => $model->getDefaultItems(),
                'jsonData' => ['home' => $model->getDefaultPage(), 'items' => []],
                'opts' => [
                    'catgory' => [],
                ]
            ]);
        }
        // 接收post数据
        $post = $this->postData();
        if (!$model->add(json_decode($post['params'], true))) {
            return $this->renderError($model->getError() ?:'添加失败');
        }
        return $this->renderSuccess('添加成功');
    }

    /**
     * 首页编辑
     * @return \think\response\Json
     */
    public function home(){
        return $this->edit();
    }
    /**
     * 编辑页面
     */
    public function edit($page_id = null)
    {
        $model = $page_id > 0 ? PageModel::detail($page_id) : PageModel::getHomePage();
        if ($this->request->isGet()) {
            $jsonData = (new PageModel())->getPageData($model['page_data']);
            jsonRecursive($jsonData);
            return $this->renderSuccess('', [
                'defaultData' => $model->getDefaultItems(),
                'jsonData' => $jsonData,
                'opts' => [
                    'catgory' => [],
                ]
            ]);
        }

        // 接收post数据
        $post = $this->postData();
        if (!$model->edit(json_decode($post['params'], true))) {
            return $this->renderError($model->getError() ?:'更新失败');
        }
        return $this->renderSuccess('更新成功');
    }

    /**
     * 删除页面
     */
    public function delete($page_id)
    {
        // 帮助详情
        $model = PageModel::detail($page_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?:'删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 分类模板
     */
    public function category()
    {
        $model = PageCategoryModel::detail();
        if($this->request->isGet()){
            return $this->renderSuccess('', compact('model'));
        }
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }
    /**
     * 商城设置
     */
    public function nav()
    {
        if($this->request->isGet()){
            $data = SettingModel::getItem('nav');
            return $this->renderSuccess('', compact('data'));
        }
        $model = new SettingModel;
        $data = $this->postData();
        if ($model->edit('nav', $data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }
    /**
     * 商城设置
     */
    public function bottomnav()
    {
        if($this->request->isGet()){
            $data = SettingModel::getItem(SettingEnum::BOTTOMNAV);
            return $this->renderSuccess('', compact('data'));
        }
        $model = new SettingModel;
        $data = $this->postData();
        if ($model->edit(SettingEnum::BOTTOMNAV, $data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    public function bottomedit(){
        $model = new SettingModel;
        $data = $this->postData();
        $vars = SettingModel::getItem(SettingEnum::BOTTOMNAV);
        if($data['type'] == 'image'){
            $vars['menus'][$data['index']] = [
                'index' => $data['index'],
                'text' => $data['text'],
                'iconPath' => $data['iconPath'],
                'selectedIconPath' => $data['selectedIconPath'],
            ];
        } else if($data['type'] == 'color'){
            $vars['color'] = $data['color'];
            $vars['no_color'] = $data['no_color'];
        }
        if ($model->edit(SettingEnum::BOTTOMNAV, $vars)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }
}
