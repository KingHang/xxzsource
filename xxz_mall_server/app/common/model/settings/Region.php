<?php

namespace app\common\model\settings;

use think\facade\Cache;
use app\common\library\helper;
use app\common\model\BaseModel;
/**
 * 地区模型
 */
class Region extends BaseModel
{
    protected $name = 'region';
    protected $pk = 'id';
    protected $createTime = false;
    protected $updateTime = false;

    /**
     * 类型自动转换
     * @var array
     */
    protected $type = [
        'id' => 'integer',
        'pid' => 'integer',
        'level' => 'integer',
    ];

    // 当前数据版本号
    private static $version = '1.2.3';

    // 县级市别名 (兼容微信端命名)
    private static $county = [
        '省直辖县级行政区划',
        '自治区直辖县级行政区划',
    ];

    /**
     * 根据id获取地区名称
     */
    public static function detail($id)
    {
        return (new static())->find($id);
    }

    /**
     * 根据id获取地区名称
     */
    public static function getNameById($id)
    {
        return $id > 0 ? self::getCacheAll()[$id]['name'] : '';
    }

    /**
     * 根据名称获取地区id
     */
    public static function getIdByName($name, $level = 0, $pid = 0)
    {
        // 兼容：微信端"省直辖县级行政区划"
        if (in_array($name, static::$county)) {
            $name = '直辖县级';
        }
        $data = self::getCacheAll();
        foreach ($data as $item) {
            if ($item['name'] == $name && $item['level'] == $level && $item['pid'] == $pid)
                return $item['id'];
        }
        return 0;
    }

    /**
     * 获取所有地区(树状结构)
     */
    public static function getCacheTree()
    {
        return static::getCacheData('tree');
    }

    /**
     * 获取所有地区列表
     */
    public static function getCacheAll()
    {
        return static::getCacheData('all');
    }

    /**
     * 获取所有地区的总数
     */
    public static function getCacheCounts()
    {
        return static::getCacheData('counts');
    }

    /**
     * 获取缓存中的数据(存入静态变量)
     */
    private static function getCacheData($item = null)
    {
        static $cacheData = [];
        if (empty($cacheData)) {
            $static = new static;
            $cacheData = $static->regionCache();
        }
        if (is_null($item)) {
            return $cacheData;
        }
        return $cacheData[$item];
    }

    /**
     * 获取地区缓存
     */
    private function regionCache()
    {
        // 缓存的数据
        $complete = Cache::get('region');
        // 如果存在缓存则返回缓存的数据，否则从数据库中查询
        // 条件1: 获取缓存数据
        // 条件2: 数据版本号要与当前一致
        if (
            !empty($complete)
            && isset($complete['version'])
            && $complete['version'] == self::$version
        ) {
            return $complete;
        }
        // 所有地区
        $allList = $tempList = $this->getAllList();
        // 已完成的数据
        $complete = [
            'all' => $allList,
            'tree' => $this->getTreeList($allList),
            'counts' => $this->getCount($allList),
            'version' => self::$version,
        ];
        // 写入缓存
        Cache::tag('cache')->set('region', $complete);
        return $complete;
    }

    private static function getCount($allList)
    {
        $counts = [
            'total' => count($allList),
            'province' => 0,
            'city' => 0,
            'region' => 0,
        ];
        $level = [1 => 'province', 2 => 'city', 3 => 'region'];
        foreach ($allList as $item) {
            $counts[$level[$item['level']]]++;
        }
        return $counts;
    }

    /**
     * 格式化为树状格式
     */
    private function getTreeList($allList)
    {
        $treeList = [];
        foreach ($allList as $pKey => $province) {
            if ($province['level'] == 1) {    // 省份
                $treeList[$province['id']] = $province;
                unset($allList[$pKey]);
                foreach ($allList as $cKey => $city) {
                    if ($city['level'] == 2 && $city['pid'] == $province['id']) {    // 城市
                        $treeList[$province['id']]['city'][$city['id']] = $city;
                        unset($allList[$cKey]);
                        foreach ($allList as $rKey => $region) {
                            if ($region['level'] == 3 && $region['pid'] == $city['id']) {    // 地区
                                $treeList[$province['id']]['city'][$city['id']]['region'][$region['id']] = $region;
                                unset($allList[$rKey]);
                            }
                        }
                    }
                }
            }
        }
        return $treeList;
    }

    /**
     * 从数据库中获取所有地区
     */
    private function getAllList()
    {
        $list = self::withoutGlobalScope()
            ->field('id, pid, name, level')
            ->select()
            ->toArray();
        return helper::arrayColumn2Key($list, 'id');
    }

    /**
     * 地区组装供前端使用
     */
    public static function getRegionForApi(){
        $province_arr = [];
        $city_arr = [];
        $area_arr = [];
        $region = self::getCacheTree();
        foreach ($region as $province){
            $value = [
                'label' => $province['name'],
                'value' => $province['id']
            ];
            array_push($province_arr, $value);

            $city_arr_temp = [];
            $city_area_temp = [];
            if(!isset($province['city'])){
                continue;
            }
            foreach ($province['city'] as $city){
                $value = [
                    'label' => $city['name'],
                    'value' => $city['id']
                ];
                array_push($city_arr_temp, $value);
                $area_arr_temp = [];
                if(isset($city['region'])){
                    foreach ($city['region'] as $area) {
                        $value = [
                            'label' => $area['name'],
                            'value' => $area['id']
                        ];
                        array_push($area_arr_temp, $value);
                    }
                }
                array_push($city_area_temp, $area_arr_temp);
            }
            array_push($area_arr, $city_area_temp);
            array_push($city_arr, $city_arr_temp);
        }
        return [$province_arr, $city_arr, $area_arr];
    }
    public static function getRegion($pid = 0)
    {
        $list = self::withoutGlobalScope()
            ->field('id, pid, name, level')
            ->where('pid' , '=' , $pid)
//            ->fetchSql()
            ->select()
            ->toArray();
        return $list;
    }

    /**
     * 根据首字母获取对应的城市信息
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getCityList()
    {
        $region = static::withoutGlobalScope()->field('id,name,shortname,first')->where(array('level' => 2 ))->select();
        return $region;
    }

    public static function getHotCity()
    {
        return $hotCity = array(
            ['id' => 2 , 'name' => '北京市' , 'shortname' => '北京'],
            ['id' => 802 , 'name' => '上海市' , 'shortname' => '上海'],
            ['id' => 1965 , 'name' => '广州市' , 'shortname' => '广州'],
            ['id' => 1988 , 'name' => '深圳市' , 'shortname' => '深圳'],
            ['id' => 2368 , 'name' => '成都市' , 'shortname' => '成都'],
            ['id' => 821 , 'name' => '南京市' , 'shortname' => '南京'],
            ['id' => 861 , 'name' => '苏州市' , 'shortname' => '苏州'],
            ['id' => 934 , 'name' => '杭州市' , 'shortname' => '杭州'],
            ['id' => 1710 , 'name' => '武汉市' , 'shortname' => '武汉'],
            ['id' => 2324 , 'name' => '重庆市' , 'shortname' => '重庆'],
            ['id' => 2899 , 'name' => '西安市' , 'shortname' => '西安'],
            ['id' => 1047 , 'name' => '合肥市' , 'shortname' => '合肥'],
        );
    }
    /**
     * 根据名称获取地区id
     */
    public static function getIdLikeName($name, $level = 0)
    {
        if ($name == '') {
            return 0;
        }
        // 兼容：微信端"省直辖县级行政区划"
        if (in_array($name, static::$county)) {
            $name = '直辖县级';
        }
        $region_id = self::withoutGlobalScope()
            ->where('level' , '=' , $level)
            ->whereLike('shortname|name' , '%' . $name . '%')
            ->value('id');
        return intval($region_id);
    }
}
