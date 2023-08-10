<?php

namespace app\mall\model\page;

use app\common\model\home\Home as PageModel;
use app\api\model\goods\Goods as ProductModel;
use app\mall\model\app\App;
use think\db\exception\DbException;
use think\Paginator;

/**
 * 微信小程序diy页面模型
 */
class Home extends PageModel
{
    /**
     * 获取列表
     * @param $params
     * @return Paginator
     * @throws DbException
     */
    public function getList($params)
    {
        return $this->where(['is_delete' => 0])
            ->where(['page_type' => 20])
            ->order(['create_time' => 'desc'])
            ->hidden(['page_data'])
            ->paginate($params);
    }

    /**
     * 获取所有自定义页面
     */
    public function getLists()
    {
        return $this->where(['is_delete' => 0])
            ->where(['page_type' => 20])
            ->hidden(['page_data'])
            ->order(['create_time' => 'desc'])
            ->select();
    }

    /**
     * 新增页面
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        // 删除app缓存
        App::deleteCache();
        return $this->save([
            'page_type' => 20,
            'page_name' => $data['page']['params']['name'],
            'page_data' => $data,
            'app_id' => self::$app_id
        ]);
    }

    /**
     * 更新页面
     * @param $data
     * @return bool
     */
    public function edit($data)
    {
        // 删除app缓存
        App::deleteCache();
        // 保存数据
        return $this->save([
                'page_name' => $data['page']['params']['name'],
                'page_data' => $data
            ]) !== false;
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 处理页面数据
     * @param $page_data
     * @return array
     */
    public function getPageData($page_data)
    {
        if (!empty($page_data['items'])) {
            foreach ($page_data['items'] as $key => $item) {
                if ($item['type'] === 'product') {
                    $page_data['items'][$key]['data'] = $this->getProductList($item);
                }
            }
        }

        return $page_data;
    }

    /**
     * 商品组件：获取商品列表
     * @param $item
     * @return array
     */
    private function getProductList($item)
    {
        // 获取商品数据
        $model = new ProductModel;
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $productIds = array_column($item['data'], 'product_id');
            $productList = $model->getListByIdsFromApi($productIds);
        } else {
            // 数据来源：自动
            $productList = $model->getList([
                'type' => 'sell',
                'category_id' => $item['params']['auto']['category'],
                'sortType' => $item['params']['auto']['productSort'],
                'list_rows' => $item['params']['auto']['showNum'],
                'audit_status' => 10
            ]);
        }
        if ($productList->isEmpty()) return [];
        // 格式化商品列表
        $data = [];
        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
            ];
        }
        return $data;
    }
}
