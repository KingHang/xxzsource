<?php


namespace app\common\model\store;

use app\common\model\BaseModel;

/**
 * 门店店员模型
 */
class Clerk extends BaseModel
{
    protected $pk = 'clerk_id';
    protected $name = 'store_clerk';

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->BelongsTo('app\common\model\user\User', 'user_id', 'user_id');
    }

    /**
     * 关联门店表
     */
    public function store()
    {
        return $this->BelongsTo('app\\common\\model\\store\\Store', 'store_id', 'store_id');
    }

    /**
     * 店员详情
     */
    public static function detail($where)
    {
        $filter = is_array($where) ? $where : ['clerk_id' => $where];
        return (new static())->where(array_merge(['is_delete' => 0], $filter))->find();
    }

    /**
     * 状态
     */
    public function getStatusAttr($value)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return ['text' => $status[$value], 'value' => $value];
    }
    /**
     * 验证用户是否为核销员
     */
    public function checkUser($store_id , $type = 0)
    {
        if ($this['is_delete']) {
            $this->error = '未找到店员信息';
            return false;
        }
        // 自提验证
        if (($type == 0 && $this['store_id'] != $store_id)
            || ($type == 1 && !empty($store_id) && !in_array($this['store_id'],$store_id))
            || ($type == 2 && (empty($store_id) || !in_array($this['store_id'],$store_id)))
        ) {
            $this->error = $type == 0 ? '当前店员不属于该门店，没有核销权限' : '当前门店店员没有核销此商品权限';
            return false;
        }
        if (!$this['status']) {
            $this->error = '当前店员状态已被禁用';
            return false;
        }
        return true;
    }
}