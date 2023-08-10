<?php

namespace app\mall\controller\link;

use app\mall\controller\Controller;
use app\mall\model\plugin\groupsell\Active;
use app\mall\model\plugin\flashsell\Active as ActiveModel;
use app\mall\model\page\Home as PageModel;
use app\mall\model\plugin\table\Table as TableModel;

/**
 * Class Link
 * @package app\mall\controller\link
 * 超链接控制器
 */
class Link extends Controller
{
    /**
     *获取数据
     */
    public function index($activeName)
    {
        // 万能表单
        $list = (new TableModel())->getAll();
        $tableList = [];
        foreach ($list as $item) {
            $tableList[] = [
                'id' => $item['table_id'],
                'url' => 'pages/plugin/table/table?table_id=' . $item['table_id'],
                'name' => $item['name'],
                'type' => '表单'
            ];
        }
        return $this->renderSuccess('', compact('tableList'));
    }

    /**
     * 获取自定义页面
     */
    public function getPageList()
    {
        $model = new PageModel;
        $list = $model->getLists();
        return $this->renderSuccess('', compact('list'));
    }
}
