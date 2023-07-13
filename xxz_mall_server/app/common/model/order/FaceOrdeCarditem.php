<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
/**
 * 卡项订单模型
 */
class FaceOrdeCarditem  extends BaseModel
{
    protected $name = 'face_order_carditem';
    protected $pk = 'id';
    public function getValidPeriodAttr($value)
    {
        $valueData = array();
        if (!empty($value)) {
            $valueArr = json_decode($value,true);
            $valueData['valueArr'] = $valueArr;
            if (!empty($valueArr)) {
                switch ($valueArr['type']) {
                    case 0:
                        $valueData['text'] = '永久有效';
                        break;
                    case 1;
                        $valueData['text'] = '有效期至：'. $valueArr['value'];
                        break;
                    case 2:
                        $valueData['text'] = '购买后'. $valueArr['value'] . "天内有效";
                        break;
                    case 3:
                        $valueData['text'] = '首次使用后'. $valueArr['value'] . "天内有效";
                        break;
                }
            }
        }
        return $valueData;
    }
    /**
     * 获取指定商品销量（d/today:天，w/week:周，m/month:月，y/year:年，yesterday：昨天，last week：上周，last month：上个月，last year:去年）
     * @param string $data_type
     * @param $card_id
     * @return float|int
     */
    public function getDataSalesVolume($data_type = 'week',$card_id)
    {
        $model = $this;
        // 使用日期表达式
        switch (strtolower($data_type)) {
            case 'today':
            case 'd':
                $start_time = strtotime(date("Y-m-d",time()));
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'week':
            case 'w':
                $start_time = strtotime('-7 day');
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'month':
            case 'm':
                $start_time = mktime(0,0,0,1,date('m'),date('Y'));
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'year':
            case 'y':
                $start_time = mktime(0,0,0,1,1,date('Y'));
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'yesterday':
                $start_time = strtotime(date("Y-m-d",time()));
                $end_time = mktime(0,0,0,date('d')+1,date('m'),date('Y'));
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            case 'last week':
                $start_time = strtotime('-14 day');
                $end_time = strtotime('-7 day');
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            case 'last month':
                $start_time = strtotime('-30 day');
                $end_time = time();
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            case 'last year':
                $start_time = mktime(0,0,0,1,1,date('Y')-1);
                $end_time = mktime(0,0,0,1,1,date('Y'));
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            default:
                return 0;
        }
        return $model->alias('a')
            ->join('order order', 'a.order_id = order.order_id','left')
            ->where(array('order.pay_status' => 20 , 'a.card_id' => $card_id ))
//            ->fetchSql()
            ->sum('a.total_num');
    }
    /**
     * 关联卡项服务表
     */
    public function Carditem()
    {
        return $this->hasMany('app\\common\\model\\facerecognition\\Carditem', 'id', 'card_id');
    }
}