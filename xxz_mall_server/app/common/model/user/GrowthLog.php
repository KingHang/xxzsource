<?php

namespace app\common\model\user;

use app\common\model\BaseModel;
use Exception;
use think\Collection;

class GrowthLog extends BaseModel
{
    protected $name = 'user_growth_log';

    protected $pk = 'log_id';

    protected $updateTime = false;

    public function getTypeAttr($value)
    {
        $valueData = array('value' => $value);

        switch ($value) {
            case 0:
                $valueData['text'] = '订单赠送';
                break;
            case 1;
                $valueData['text'] = '日常签到';
                break;
            case 2:
                $valueData['text'] = '注册奖励';
                break;
            case 3:
                $valueData['text'] = '阅读奖励';
                break;
            case 4:
                $valueData['text'] = '转发奖励';
                break;
            case 5:
                $valueData['text'] = '投稿奖励';
                break;
            case 6:
                $valueData['text'] = '补签';
                break;
            case 7:
                $valueData['text'] = '邀请奖励';
                break;
        }

        return $valueData;
    }

    /**
     * 关联会员记录表
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\user\\User");
    }

    /**
     * 新增记录
     * @param $data
     */
    public static function add($data)
    {
        $static = new static;
        $static->save(array_merge(['app_id' => $static::$app_id], $data));
    }

    /**
     * 新增记录 (批量)
     * @param $saveData
     * @return Collection
     * @throws Exception
     */
    public function onBatchAdd($saveData)
    {
        return $this->saveAll($saveData);
    }
}
