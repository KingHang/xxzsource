<?php

namespace app\api\model\purveyor;

use app\common\model\purveyor\Purveyor as SupplierModel;
use app\common\model\purveyor\User as SupplierUserModel;
use app\api\model\user\Favorite as FavoriteModel;
use app\api\model\goods\Goods as ProductModel;

/**
 * 供应商模型类
 */
class Purveyor extends SupplierModel
{
    /**
     * 添加
     */
    public function addData($data)
    {
        // 开启事务
        $this->startTrans();
        try {
            if (SupplierUserModel::checkExist($data['user_name'])) {
                $this->error = '用户名已存在';
                return false;
            }
            // 添加供应商
            $this->save($data);
            //添加供应商账号
            $SupplierUserModel = new SupplierUserModel;
            $data['purveyor_id'] = $this['purveyor_id'];
            $data['is_super'] = 1;
            $SupplierUserModel->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function getUserName($user_name)
    {
        return $this->where('user_name', '=', $user_name)->count();
    }

    //获取店铺信息
    public function getDetail($data, $user)
    {
        $detail = $this->alias('s')->where(['purveyor_id' => $data['shop_supplier_id']])
            ->field("name as store_name,purveyor_id as shop_supplier_id,purveyor_id,logo_id,category_id,server_score,fav_count,user_id,product_sales")
            ->with(['logo', 'category'])
            ->find();
        if ($detail) {
            $detail['logos'] = $detail['logo']['file_path'];
            $detail['category_name'] = $detail['category']['name'];
            unset($detail['logo']);
            unset($detail['category']);
            $detail['isfollow'] = 0;
            $detail['supplier_user_id'] = (new SupplierUserModel())->where('purveyor_id', '=', $detail['purveyor_id'])->value('purveyor_user_id');

            if ($user) {
                $detail['isfollow'] = (new FavoriteModel)
                    ->where('pid', '=', $data['shop_supplier_id'])
                    ->where('user_id', '=', $user['user_id'])
                    ->where('type', '=', 10)
                    ->count();
            }
        }
        return $detail;
    }

    //获取微店账号信息
    public function getAccount($shop_supplier_id, $field = "*")
    {
        $detail = $this->where(['purveyor_id' => $shop_supplier_id])->field("$field")->find();
        return $detail;
    }

    //店铺列表
    public function supplierList($param, $user)
    {
        // 排序规则
        $sort = [];
        if ($param['sortType'] === 'all') {
            $sort = ['s.create_time' => 'desc'];
        } else if ($param['sortType'] === 'sales') {
            $sort = ['product_sales' => 'desc'];
        } else if ($param['sortType'] === 'score') {
            $sort = ['server_score' => 'desc'];
        }

        $model = $this;
        if (isset($param['name']) && $param['name']) {
            $model = $model->where('name', 'like', '%' . $param['name'] . '%');
        }
        // 查询列表数据
        $list = $model->alias('s')->with(['logo', 'category'])
            ->where('s.is_delete', '=', '0')
            ->where('s.is_recycle', '=', 0)
            //->where('s.is_full', '=', 1)
            ->field("s.purveyor_id as shop_supplier_id ,  s.purveyor_id,s.name,s.fav_count,logo_id,category_id,server_score,product_sales")
            ->order($sort)
            ->paginate($param);
        $product_model = new ProductModel();
        foreach ($list as $key => &$v) {
            $productList = $product_model->with(['image.file'])
                ->where([
                    'purveyor_id' => $v['shop_supplier_id'],
                    'product_status' => 10,
                    'audit_status' => 10,
                    'is_delete' => 0
                ])
                ->order('product_sort asc,product_id desc')
                ->limit(3)
                ->field('goods_id as product_id,goods_id,product_price,product_name,sales_initial,sales_actual,line_price,product_type,store_ids,purveyor_id')
                ->select();
            $v['productList'] = $productList;
            $v['logos'] = $v['logo']['file_path'];
            $v['category_name'] = $v['category']['name'];
            $v['isfollow'] = (new FavoriteModel)
                ->where('pid', '=', $v['shop_supplier_id'])
                ->where('user_id', '=', $user['user_id'])
                ->where('type', '=', 10)
                ->count();
            unset($v['logo']);
            unset($v['category']);
        }
        return $list;
    }
}
