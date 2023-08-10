<?php

namespace app\super\controller;
/**
 * 后台首页
 */
class Index extends Controller
{
    /**
     * 后台首页
     */
    public function index()
    {
        $version = get_version();
        return $this->renderSuccess('', compact('version'));
    }
}