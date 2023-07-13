<?php
declare (strict_types = 1);

namespace app\api\model\plus\articlepromotion;

use think\Model;
use app\common\model\plugin\articlepromotion\Poster as PosterModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * @mixin \think\Model
 */
class Poster extends PosterModel
{
    public static function detail($poster_id)
    {
        if (!$model = parent::detail($poster_id)) {
            throw new BaseException(['msg' => '海报不存在']);
        }
        return $model;
    }
    public function getList($params, $category_id = 0)
    {
        $model = $this;
        $page = $params['home'] ?? '';//页码
        $pageSize = $params['pageSize'] ?? 10;//页码
        $category_id > 0 && $model = $model->where('category_id', '=', $category_id);
        return $model ->with(['image', 'category'])
            ->where('poster_status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate(['list_rows' => $pageSize, 'home' => $page]);
    }
}
