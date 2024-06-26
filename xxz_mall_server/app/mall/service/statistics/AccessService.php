<?php

namespace app\mall\service\statistics;

use app\mall\model\user\Favorite as FavoriteModel;
use app\mall\model\user\Visit as VisitModel;
/**
 * 供应商数据概况
 */
class AccessService
{
    /**
     * 获取数据概况
     */
    public function getData()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $data = [
            // 商品收藏数
            'product_fav_total' => [
                'today' => number_format($this->getFavData(null, $today, 'goods')),
                'yesterday' => number_format($this->getFavData(null, $yesterday, 'goods'))
            ],
            // 访客数
            'visit_user' => [
                'today' => number_format($this->getVisitData($today, null, 'visit_user')),
                'yesterday' => number_format($this->getVisitData($yesterday, null, 'visit_user'))
            ],
            // 访问量
            'visit_count' => [
                'today' => number_format($this->getVisitData($today, null, 'visit_count')),
                'yesterday' => number_format($this->getVisitData($yesterday, null, 'visit_count'))
            ],
        ];
        return $data;
    }
    /**
     * 通过时间段查询用户数据
     */
    public function getVisitDataByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'visit_user' => $this->getVisitData($day, null, 'visit_user'),
                'visit_count' => $this->getVisitData($day, null, 'visit_count')
            ];
        }
        return $data;
    }

    /**
     * 通过时间段查询用户数据
     */
    public function getFavDataByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'supplier_count' => $this->getFavData($day, null, 'purveyor'),
                'product_count' => $this->getFavData($day, null, 'goods')
            ];
        }
        return $data;
    }

    /**
     * 获取用户数据
     */
    private function getVisitData($startDate = null, $endDate = null, $type)
    {
        return (new VisitModel())->getVisitData($startDate, $endDate, $type);
    }

    /**
     * 收藏数
     */
    private function getFavData($startDate = null, $endDate = null, $type){
        return (new FavoriteModel())->getFavData($startDate, $endDate, $type);
    }
}