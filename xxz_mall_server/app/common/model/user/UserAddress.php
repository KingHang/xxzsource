<?php

namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\settings\Region;

class UserAddress extends BaseModel
{
    protected $name = 'user_address';
    protected $pk = 'address_id';
    /**
     * 追加字段
     * @var array
     */
    protected $append = ['region'];

    /**
     * 地区名称
     */
    public function getRegionAttr($value, $data)
    {
        return [
            'province' => Region::getNameById($data['province_id']),
            'city' => Region::getNameById($data['city_id']),
            'region' => $data['region_id'] == 0 ? $data['district']
                : Region::getNameById($data['region_id']),
        ];
    }
    /**
     * 获取列表
     */
    public function getList($user_id,$default_id = 0)
    {
        $field = "*";
        $list = $this->where('user_id', '=', $user_id);
        if ($default_id > 0) {
            $field = "*,(CASE WHEN address_id = " . $default_id . " THEN 1 ELSE 0 END) as default_address";
            $list->order('default_address DESC');
        }
        return $list->field($field)->select();
    }

    /**
     * 新增收货地址
     */
    public function add($user, $data)
    {
        $data['gender'] = isset($data['gender']) && in_array($data['gender'],array(1,2)) ? $data['gender'] : 1;
        $data['isdefault'] = isset($data['isdefault']) && $data['isdefault'] == 'true' ? 1 : 0;
        $region = array(
            'province' => Region::getNameById($data['province_id']),
            'city'     => Region::getNameById($data['city_id']),
            'region'   => Region::getNameById($data['region_id']),
        );
        // 添加收货地址
        $this->startTrans();
        try {
            //当前设为默认地址，把其他的设为非默认
            if ($data['isdefault']) {
                $this->where(['user_id' => $user['user_id']])->update(['is_default' => 0]);
            }
            else{
                //如果本次默认地址为false，查有没有默认地址 如果没有将本条设置为默认地址
                $existDefault1 = $this->where(['user_id' => $user['user_id'], 'is_default' => 1])->find();
                if (!$existDefault1) {
                    $data['isdefault']=1;
                }
            }

            $address_id = $this->insertGetId([
                'name'        => $data['name'],
                'phone'       => $data['phone'],
                'province_id' => $data['province_id'],
                'city_id'     => $data['city_id'],
                'region_id'   => $data['region_id'],
                'province'    => $region['province'],
                'city'        => $region['city'],
                'region'      => $region['region'],
                'detail'      => $data['detail'],
                'district'    => ($data['region_id'] === 0 && !empty($region[2])) ? $region[2] : '',
                'user_id'     => $user['user_id'],
                'gender'      => $data['gender'],
                'is_default'  => $data['isdefault'],
                'app_id'      => self::$app_id
            ]);
            // 判断是否存在默认收货地址
            if (!$data['isdefault']) {
                $existDefault = $this->where(['user_id' => $user['user_id'], 'is_default' => 1])->find();
                if (!$existDefault) {
                    $user->save(['address_id' => 0]);
                }
            } else {
                $user->save(['address_id' => $address_id]);
            }
            $this->commit();
            return $this->where('address_id', '=', $address_id)->field('*')->find();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 编辑收货地址
     */
    public function edit($user, $data)
    {
        $data['gender'] = isset($data['gender']) && in_array($data['gender'],array(1,2)) ? $data['gender'] : 1;
        $data['isdefault'] = isset($data['isdefault']) && $data['isdefault'] == 'true' ? 1 : 0;
        $arr = array(
            'name'       => $data['name'],
            'phone'      => $data['phone'],
            'detail'     => $data['detail'],
            'gender'     => $data['gender'],
            'is_default' => $data['isdefault']
        );
        // 添加收货地址
        if (isset($data['region']) && !empty($data['region'])) {
            $region = explode(',', $data['region']);
            $arr['province_id'] = Region::getIdByName($region[0], 1);
            $arr['city_id'] = Region::getIdByName($region[1], 2, $arr['province_id']);
            $arr['region_id'] = Region::getIdByName($region[2], 3, $arr['city_id']);
            $arr['district'] = ($arr['region_id'] === 0 && !empty($region[2])) ? $region[2] : '';
        } else {
            $arr['province'] = Region::getNameById($data['province_id']);
            $arr['city'] = Region::getNameById($data['city_id']);
            $arr['region'] = Region::getNameById($data['region_id']);
        }

        $this->startTrans();
        try {
            //当前设为默认地址，把其他的设为非默认
            if ($data['isdefault']) {
                $this->where(['user_id' => $user['user_id']])->update(['is_default' => 0]);
            }

            $this->save($arr);

            // 判断是否存在默认收货地址
            if (!$data['isdefault']) {
                $existDefault = $this->where(['user_id' => $user['user_id'], 'is_default' => 1])->find();
                if (!$existDefault) {
                    $user->save(['address_id' => 0]);
                }
            } else {
                $this->setDefault($user);
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 设为默认收货地址
     */
    public function setDefault($user)
    {
        // 设为默认地址
        return $user->save(['address_id' => $this['address_id']]);
    }

    /**
     * 删除收货地址
     * @return bool
     * @throws \Exception
     */
    public function remove($user)
    {
        // 查询当前是否为默认地址
        $user['address_id'] == $this['address_id'] && $user->save(['address_id' => 0]);
        return $this->delete();
    }

    /**
     * 收货地址详情
     */
    public static function detail($user_id, $address_id)
    {
        $where = ['user_id' => $user_id, 'address_id' => $address_id];
        return (new static())->where($where)->find();
    }
}