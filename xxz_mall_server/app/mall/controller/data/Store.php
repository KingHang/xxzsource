<?php

namespace app\mall\controller\data;

use app\mall\controller\Controller;
use app\mall\model\store\Store as StoreModel;

class Store extends Controller
{
    /**
     * 门店列表
     */
    public function lists()
    {
        $list = (new StoreModel())->getAllList();
        return $this->renderSuccess('', compact('list'));
    }
}
