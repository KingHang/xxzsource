<?php


namespace app\common\model\store;

use app\common\model\BaseModel;
use app\common\model\setting\Region as RegionModel;


/**
 * 门店订单模型
 */
class Store extends BaseModel
{
    protected $pk = 'store_id';

    protected $name = 'store';

    protected $append = ['region'];

    /**
     * 关联门店logo
     */
    public function logo()
    {
        return $this->hasOne("app\\common\\model\\file\\UploadFile", 'file_id', 'logo_image_id');
    }

    /**
     * 关联供应商表
     */
    public function supplier()
    {
        return $this->belongsTo('app\\common\\model\\purveyor\\Purveyor', 'purveyor_id', 'purveyor_id');
    }

    /**
     * 地区名称
     */
    public function getRegionAttr($value, $data)
    {
        return [
            'province' => RegionModel::getNameById($data['province_id']),
            'city' => RegionModel::getNameById($data['city_id']),
            'region' => $data['region_id'] == 0 ? '' : RegionModel::getNameById($data['region_id']),
        ];
    }


    /**
     * 门店状态
     */
    public function getStatusAttr($value)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 是否支持自提核销
     */
    public function getIsCheckAttr($value)
    {
        $status = [0 => '不支持', 1 => '支持'];
        return ['text' => $status[$value], 'value' => $value];
    }


    /**
     * 门店详情
     */
    public static function detail($store_id)
    {
        return (new static())->with(['logo'])->where('store_id','=',$store_id)->find();
    }

    /**
     * 门店列表
     */
    public static function getStoreList($field,$shop_supplier_id,$param = [])
    {
        $where = array (
            'is_delete' => 0,
            'status' => 1
        );
	    $order = ['create_time' => 'desc'];
        if ($shop_supplier_id > 0) {
            $where['purveyor_id'] = $shop_supplier_id;
        }
        $field = !empty($field) ? $field . ",province_id,city_id,region_id" : '*';
        $model = new static;
        if (isset($param['store_name']) && $param['store_name']) {
            $model = $model->where('store_name', 'like', '%' . $param['store_name'] . '%');
        }
        if (isset($param['latitude']) && isset($param['longitude'])) {
            $latitude = $param['latitude'];
            $longitude = $param['longitude'];
            $field .= ",6378.137*2*ASIN(SQRT(POW(SIN(({$latitude} * PI()/180-latitude*PI()/180)/2),2) + COS({$latitude}*PI()/180)*COS(latitude*PI()/180)*POW(SIN(({$longitude} * PI()/180-longitude*PI()/180)/2),2)))*1000 AS distance";
            $order =['distance' => 'asc', 'create_time' => 'desc'];
        }

        // 查询列表数据
        $list = $model->where($where)->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->field($field)
            ->order($order);
        if (isset($param['search_type']) && $param['search_type'] == 1) {
            $list = $list->select();
        } else {
            $list = $list->paginate($param);
        }
        foreach ($list as &$store) {
            if ($store['distance'] >= 1000) {
                $distance = bcdiv($store['distance'], 1000, 2);
                $store['distance'] = $distance . 'km';
            } else {
                $store['distance'] = round($store['distance'], 2) . 'm';
            }
        }
        return $list;
    }
    /**
     * 获取门店列表
     */
    public function getList($is_check = null, $longitude = '', $latitude = '', $limit = false, $shop_supplier_id = 0,$keyword='')
    {
        $model = $this;
        // 是否支持自提核销
        $is_check && $model = $model->where('is_check', '=', $is_check);
        //搜索门店
        $keyword && $model = $model->where('store_name', 'like', '%' . $keyword . '%');
        // 商家id
        $shop_supplier_id && $model = $model->whereIn('purveyor_id', $shop_supplier_id);
        // 获取数量
        $limit != false && $model = $model->limit($limit);
        // 获取门店列表数据
        $data = $model->where('is_delete', '=', '0')
            ->where('status', '=', '1')
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->select();
        // 根据距离排序
        if (!empty($longitude) && !empty($latitude)) {
            return $this->sortByDistance($data, $longitude, $latitude);
        }
        return $data;
    }

    /**
     * 根据距离排序
     */
    public function sortByDistance(&$data, $longitude, $latitude,$source = 0)
    {
        // 根据距离排序
        if ($source == 0) {
            $list = $data->isEmpty() ? [] : $data->toArray();
        } else {
            $list = empty($data) ? [] : $data;
        }

        $sortArr = [];
        foreach ($list as &$store) {
            // 计算距离
            $distance = !empty($store['longitude']) && !empty($store['latitude']) ? self::getDistance($longitude, $latitude, $store['longitude'], $store['latitude']) : 0;
            // 排序列
            $sortArr[] = $distance;
            $store['distance'] = $distance;
            if ($distance >= 1000) {
                $distance = bcdiv($distance, 1000, 2);
                $store['distance_unit'] = $distance . 'km';
            } else
                $store['distance_unit'] = $distance . 'm';
        }
        // 根据距离排序
        array_multisort($sortArr, SORT_ASC, $list);
        return $list;
    }

    /**
     * 获取两个坐标点的距离
     */
    private static function getDistance($ulon, $ulat, $slon, $slat)
    {
        // 地球半径
        $R = 6378137;
        // 将角度转为狐度
        $radLat1 = deg2rad($ulat);
        $radLat2 = deg2rad($slat);
        $radLng1 = deg2rad($ulon);
        $radLng2 = deg2rad($slon);
        // 结果
        $s = acos(cos($radLat1) * cos($radLat2) * cos($radLng1 - $radLng2) + sin($radLat1) * sin($radLat2)) * $R;
        // 精度
        $s = round($s * 10000) / 10000;
        return round($s);
    }

    /**
     * 根据门店id集获取门店列表
     */
    public function getListByIds($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = explode(',',$storeIds);
        }
        $model = $this;
        // 筛选条件
        $filter = ['store_id' => $storeIds];
        if (!empty($storeIds)) {
            $model = $model->orderRaw('field(store_id, ' . implode(',', $storeIds) . ')');
        }
        // 获取商品列表数据
        return $model->with(['logo'])
            ->where('is_delete', '=', '0')
            ->where('status', '=', '1')
            ->where($filter)
            ->select();
    }

    /**
     * 获取门店列表
     */
    public function getStoreIds($is_check = null, $shop_supplier_id = 0)
    {
        $model = $this;
        // 是否支持自提核销
        $is_check && $model = $model->where('is_check', '=', $is_check);
        // 商家id
        $shop_supplier_id && $model = $model->whereIn('shop_supplier_id', $shop_supplier_id);
        // 获取门店列表数据
        $data = $model->where('is_delete', '=', '0')
            ->where('status', '=', '1')
            ->column('store_id');
        return $data;
    }

    /**
     * 获取启用的门店数量
     * @param $shop_supplier_id
     * @return int
     */
    public function getSaleStoreNum($shop_supplier_id)
    {
        $where = ['status' => 1, 'is_delete' => 0];
        if ($shop_supplier_id > 0) {
            $where = array_merge($where, ['shop_supplier_id' => $shop_supplier_id]);
        }
        return (int)$this->where($where)->count();
    }

    /**
     * 获取统一门店列表
     * @param array $data
     * @return array
     */
    public function getUniteStoreList($data = [])
    {
        // 请求来源：server-服务，card-卡项
        $data['source'] = !isset($data['source']) ? 'server' : $data['source'];
        switch ($data['source']) {
            case 'server':
                $list = $this->getServerStoreList($data);
                break;
            case 'card':
                $list = $this->getCardStoreList($data);
                break;
            default:
                $list = [];
                break;
        }
        return $list;
    }

    /**
     * 获取服务相关门店列表
     * @param array $data
     * @return array
     */
    public function getServerStoreList($data = [])
    {
        $model = $this;

        if (isset($data['server_id']) && $data['server_id']) {
            $model = $model->where('server.server_id', '=', $data['server_id']);
        }

        if (isset($data['shop_supplier_id']) && $data['shop_supplier_id']) {
            $model = $model->where('store.purveyor_id', '=', $data['shop_supplier_id']);
        }

        if (isset($data['type']) && $data['type'] && isset($data['search']) && $data['search']) {
            if ($data['type'] == 'all') {
                // 供应商和门店
                $model = $model->where('store.store_name|purveyor.name', 'like', '%' . $data['search'] . '%');
            } else {
                // 只有门店
                $model = $model->where('store.store_name', 'like', '%' . $data['search'] . '%');
            }
        }

        $list = $model->with(['logo', 'supplier'])
            ->alias('store')
            ->leftjoin('purveyor purveyor', 'purveyor.shop_supplier_id = store.shop_supplier_id')
            ->leftjoin('face_server server', 'server.shop_supplier_id = store.shop_supplier_id or server.shop_supplier_id = 0')
            ->leftjoin('face_server_mid mid', 'mid.server_id = server.server_id and mid.store_id = store.store_id')
            ->where(function ($query) {
                $query->where('mid.id', null)
                    ->whereOr(function ($sea) {
                        $sea->where('mid.id', 'not null')->where('mid.mid_is_disabled', 1);
                    });
            })
            ->where(['store.status' => 1, 'store.is_delete' => 0])
            ->field('store.*')
            ->order(['store.sort' => 'asc', 'store.create_time' => 'desc'])
            ->paginate($data);

        if ($list) {
            foreach ($list as $key => $value) {
                $list[$key]['detail_address'] = $value['region']['province'] . $value['region']['city'] . $value['region']['region'] . $value['address'];
            }
        }

        return $list;
    }

    /**
     * 获取卡项相关门店列表
     * @param array $data
     * @return array
     */
    public function getCardStoreList($data = [])
    {
        $model = $this;

        if (isset($data['card_id']) && $data['card_id']) {
            $model = $model->where('card.id', '=', $data['card_id']);
        }

        if (isset($data['shop_supplier_id']) && $data['shop_supplier_id']) {
            $model = $model->where('store.purveyor_id', '=', $data['shop_supplier_id']);
        }

        if (isset($data['type']) && $data['type'] && isset($data['search']) && $data['search']) {
            if ($data['type'] == 'all') {
                // 供应商和门店
                $model = $model->where('store.store_name|purveyor.name', 'like', '%' . $data['search'] . '%');
            } else {
                // 只有门店
                $model = $model->where('store.store_name', 'like', '%' . $data['search'] . '%');
            }
        }

        $list = $model->with(['logo', 'supplier'])
            ->alias('store')
            ->leftjoin('purveyor purveyor', 'purveyor.shop_supplier_id = store.shop_supplier_id')
            ->leftjoin('face_carditem card', 'card.shop_supplier_id = store.shop_supplier_id or card.shop_supplier_id = 0')
            ->leftjoin('face_carditem_mid mid', 'mid.carditem_id = card.id and mid.store_id = store.store_id')
            ->where(function ($query) {
                $query->where('mid.id', null)
                    ->whereOr(function ($sea) {
                        $sea->where('mid.id', 'not null')->where('mid.mid_is_disabled', 1);
                    });
            })
            ->where(['store.status' => 1, 'store.is_delete' => 0])
            ->field('store.*')
            ->order(['store.sort' => 'asc', 'store.create_time' => 'desc'])
            ->paginate($data);

        if ($list) {
            foreach ($list as $key => $value) {
                $list[$key]['detail_address'] = $value['region']['province'] . $value['region']['city'] . $value['region']['region'] . $value['address'];
            }
        }

        return $list;
    }
}
