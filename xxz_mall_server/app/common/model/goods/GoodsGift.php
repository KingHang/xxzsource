<?php
declare (strict_types = 1);

namespace app\common\model\goods;

use app\common\model\BaseModel;
use app\common\model\file\UploadFile;
use think\Model;

/**
 * @mixin \think\Model
 */
class GoodsGift extends BaseModel
{
    protected $pk = 'goods_gift_id';
    protected $name = 'goods_gift';
    public function productgiftsku()
    {
        return $this->hasMany('app\\common\\model\\goods\\GoodsGiftSku','goods_id','goods_id');
    }
    public function product()
    {
        return $this->hasOne('app\\common\\model\\goods\\Goods','goods_id','goods_id');
    }
    public function productsku()
    {
        return $this->hasOne('app\\common\\model\\goods\\GoodsSku','goods_sku_id','goods_sku_id');
    }
    public function add($params)
    {

        $params['app_id'] = self::$app_id;
        if (!isset($params['gift']) || empty($params['gift'])) {
            $this->error = '请选择商品';
            return false;
        }
        if (!isset($params['giftsku']) || empty($params['giftsku'])) {
            $this->error = '请选择赠品';
            return false;
        }

        return $this->transaction(function () use ($params) {
            $status=0;
            // 添加领取记录
            if ($this->where('goods_id', $params['gift'][0]['goods_id'])->find()) {
                $this->error = '该商品已存在';
                return false;
            }
            foreach ($params['gift'] as $t =>$arr) {
                $arr['app_id'] = self::$app_id;
                $status = $this->create($arr);
                Goods::where(['goods_id'=>$arr['goods_id']])->save(['is_open_gift'=>$params['is_open_gift']]);
            }
            if ($status) {
                foreach ($params['giftsku'] as $K => $v) {
                    $v['app_id'] = self::$app_id;
                    GoodsGiftSku::create($v);
                    Goods::where(['goods_id'=>$v['gift_goods_id']])->save(['is_gift'=>1]);
                }
            }
            return $status;
        });
    }
    public function getList($params)
    {
        $model = $this;
        if (!empty($params['search'])) {
            if ($params['search_type'] == 1 )
            {
                $model = $model->where('goods.product_name', 'like', '%' . trim($params['search']) . '%');
            }
            if ($params['search_type'] == 2 )
            {
                $model = $model->where('product1.product_name', 'like',     '%' . trim($params['search']) . '%');
            }

        }
        if (!empty($params['is_open_gift']) || $params['is_open_gift'] === '0') {
            $model = $model->where('goods.is_open_gift', '=', $params['is_open_gift']);
        }
        $data = $model
            ->withJoin(['product'],'left')
            ->join('goodsGiftSku','xxzmall_goods_gift.goods_id=goodsGiftSku.goods_id','left')
            ->join('goods product1','goodsGiftSku.gift_goods_id=product1.goods_id','left')
            ->with(['product'=>['image'],'productGiftSku'=>['product'=>['image'=>['file']]]])
            ->group('xxzmall_goods_gift.goods_id')
            ->paginate($params);
//        var_dump($data->toArray());die;
        foreach ($data as $key => $v)
        {
            $data[$key]['product']['image'] = GoodsImage::where('goods_id',$v['goods_id'])->find();
            $data[$key]['product']['image']['file'] = UploadFile::where('file_id',$data[$key]['product']['image']['image_id'])->find();
            $product_sku_id = $this->where('goods_id',$v['goods_id'])->column('goods_sku_id');
            $product_sku = GoodsSku::where(['goods_sku_id'=>$product_sku_id])->select();
            if ($product_sku){
                foreach ($product_sku as $k =>$value)
                {
                    $product_sku[$k]['sku_msg'] = (new Goods())::getProductSku($v['product'],$value['spec_sku_id']);
                }
            }
            foreach ($v['productGiftSku'] as $t =>$arr)
            {
                $v['productGiftSku'][$t]['spec'] = (new Goods())::getProductSku($arr['product'],$arr['spec_sku_id']);
            }
            $data[$key]['product_sku'] = $product_sku;
        }
        return $data;
    }
    public function detail($gift_goods_id)
    {
        $model = $this;
        $data = $model
            ->with(['product'=>['image'=>['file']],'productGiftSku'=>['product'=>['image'=>['file']]]])
            ->where('goods_gift_id',$gift_goods_id)->find();
        foreach ($data['productGiftSku'] as $t =>$arr)
        {
            $data['productGiftSku'][$t]['spec'] = (new Goods())::getProductSku($arr['product'],$arr['spec_sku_id']);
        }

        $product_sku_id = $this->where('goods_id',$data['goods_id'])->column('goods_sku_id');
        $product_sku = GoodsSku::where(['goods_sku_id'=>$product_sku_id])->select();
        if ($product_sku){
            foreach ($product_sku as $k =>$value)
            {
                $product_sku[$k]['sku_msg'] = (new Goods())::getProductSku($data['product'],$value['spec_sku_id']);
            }
        }
        $data['product_sku'] = $product_sku;
        return $data;
    }
    public function upd($params)
    {
        $params['app_id'] = self::$app_id;
        if (!isset($params['gift']) || empty($params['gift'])) {
            $this->error = '请选择商品';
            return false;
        }
        if (!isset($params['giftsku']) || empty($params['giftsku'])) {
            $this->error = '请选择赠品';
            return false;
        }

        return $this->transaction(function () use ($params) {
            $this->where(['goods_id'=>$params['goods_id']])->delete();
            GoodsGiftSku::where(['goods_id'=>$params['goods_id']])->delete();

            if ($params['goods_id'] != $params['gift'][0]['goods_id'] && $this->where('goods_id', $params['gift'][0]['goods_id'])->find())
            {
                $this->error = '该商品已存在';
                return false;
            }
            foreach ($params['gift'] as $t =>$arr)
            {
                $arr['app_id'] = self::$app_id;
                $status = $this->create($arr);
                Goods::where(['goods_id'=>$arr['goods_id']])->save(['is_open_gift'=>$params['is_open_gift']]);
            }
            if ($status) {
                foreach ($params['giftsku'] as $K => $v)
                {
                    $v['app_id'] = self::$app_id;
                    GoodsGiftSku::create($v);
                    Goods::where(['goods_id'=>$v['gift_goods_id']])->save(['is_gift'=>1]);
                }
            }
            return $status;
        });
    }
    public function del($product_ids)
    {
        return $this->transaction(function () use ($product_ids) {
            // 添加领取记录
            $status =0;
            $this->where(['goods_id'=>$product_ids])->delete();
            $status =GoodsGiftSku::where(['goods_id'=>$product_ids])->delete();
            return $status;
        });
    }
    public function open($data)
    {
        return $this->transaction(function () use ($data) {
            // 添加领取记录
            $status =0;
            foreach ($data['product_ids'] as $ids)
            {
                $status =Goods::where(['goods_id'=>$ids])->find()->save(['is_open_gift'=>$data['is_open_gift']]);
            }

            return $status;
        });
    }
}
