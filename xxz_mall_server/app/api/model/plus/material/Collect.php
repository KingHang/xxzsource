<?php
declare (strict_types = 1);

namespace app\api\model\plus\material;

use app\common\exception\BaseException;
use app\common\model\plugin\material\Material;
use think\Request;

class Collect extends \app\common\model\plugin\material\Collect
{
    public function addCollect($data, $user)
    {
        if (!$model = (new Material())->detail($data['material_id'])) {
            throw new BaseException(['msg' => '素材不存在']);
        }
        $data['user_id'] = $user['user_id'];
        $data['app_id'] = self::$app_id;
        $log = self::where(['user_id' => $user['user_id'], 'material_id' => $data['material_id']])->find();
        if ($log) {
            if ($log->is_collect == 1){
                return $log->save(['is_collect' => 0]);
            }else{
                return $log->save(['is_collect' => 1]);
            }
        } else {
            return (new \app\common\model\plugin\material\Collect())->save($data);
        }
    }

    public function myCollect($data,$user)
    {
        $data['material_id'] = self::where(['user_id' => $user['user_id'], 'is_collect' => 1])->column('material_id');
        return (new Material())->getList($data,'',$user['user_id']);
    }
}
