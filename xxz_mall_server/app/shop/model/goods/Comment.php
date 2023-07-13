<?php

namespace app\shop\model\goods;

use app\common\model\goods\Comment as CommentModel;
use app\shop\model\purveyor\Purveyor as SupplierModel;
class Comment extends CommentModel
{
    /**
     * 软删除
     */
    public function setDelete($comment_id)
    {
        return $this->where('comment_id', '=', $comment_id)->save(['is_delete' => 1]);
    }

    /**
     * 获取评价总数量
     */
    public function getCommentTotal()
    {
        return $this->where(['is_delete' => 0])->count();
    }

    /**
     * 获取待审核商品评价总数量
     */
    public function getReviewCommentTotal()
    {
        return $this->where(['is_delete' => 0, 'status' => 0])->count();
    }


    /**
     * 更新记录
     */
    public function edit($data)
    {   
        $this->updateScore($this['shop_supplier_id']);
        return $this->where('comment_id', '=', $data['comment_id'])->save([
            'status' => $data['status'],
            'sort' => $data['sort']
        ]);
    }
    //更新店铺评分
    private function updateScore($shop_supplier_id){
        $SupplierModel = new SupplierModel;
        $express = $this->where(['shop_supplier_id' => $shop_supplier_id, 'status'=>1,'is_delete'=>0])
            ->field('round(avg(express_score),1) as score')->find();
        $server = $this->where(['shop_supplier_id' => $shop_supplier_id, 'status'=>1,'is_delete'=>0])
            ->field('round(avg(server_score),1) as score')->find();
        $describe = $this->where(['shop_supplier_id '=> $shop_supplier_id,'status'=>1,'is_delete'=>0])
            ->field('round(avg(describe_score),1) as score')->find();
        //更新店铺分数
        $SupplierModel->where('shop_supplier_id','=', $shop_supplier_id)
            ->update([
                'express_score' => $express['score'],
                'server_score' => $server['score'],
                'describe_score' => $describe['score']
            ]);

    }

}