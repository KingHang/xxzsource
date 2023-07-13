<?php
declare (strict_types = 1);

namespace app\common\model\settings;

use app\common\model\BaseModel;
use think\Model;

/**
 * @mixin \think\Model
 */
class Agreement extends BaseModel
{
    protected $name = 'agreement';

    /**
     * 获取列表
     */
    public function getList($limit = 10)
    {
        $where = [];
        if (!empty($limit['keyword']))//搜索框名字/电话
        {
            $where[] = ['keyword', 'like', '%' . $limit['keyword'] . '%'];
        }
        return $this->where($where)->paginate($limit);
    }

    /**
     * 添加新记录
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $data['shop_supplier_id'] = 10001;
        return $this->save($data);
    }

    public static function detail($id)
    {
        return (new static())->find($id);
    }

    public static function detailByKeyword($keyword)
    {
        return (new static())->where('keyword', 'like', '%' . $keyword . '%')->find();
    }

    public function upd($data)
    {
        $data['update_time'] = date("Y-m-d H:i:s");
        return self::save($data);
    }

    /**
     * 删除记录
     */
    public function remove()
    {
        return $this->delete();
    }
}
