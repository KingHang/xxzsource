<?php

namespace app\job\model\plus\live;

use app\common\model\plugin\live\Room as RoomModel;

/**
 * 直播间模型
 */
class Room extends RoomModel
{
    /**
     * 设置为开始推流
     * {"app":"59302.livepush.myqcloud.com","appid":1251815325,"appname":"live",
     * "channel_id":"1400410245_10002","errcode":0,"errmsg":"","event_time":1598429581,
     * "event_type":1,"height":0,"idc_id":33,"node":"117.68.64.13","sequence":"6930028316735004652","set_id":2,
     * "sign":"64818ed3c66765bd3df9cf81a3ea4c8c","stream_id":"1400410245_10002",
     * "stream_param":"txSecret=1ef224d69fdacd79cb4d599c854b6456&txTime=5F476B0C","t":1598430181,"user_ip":"27.18.182.202",
     * "width":0}
     */
    public function setPush($data){
        if($data['errcode'] == 0){
            $room_id = explode('_', $data['channel_id']);
            //设置最新一个房间为已开始
            $room = $this->where('qcloud_room_id', '=', 'room_'.$room_id[1])
                ->where('live_status', '=', 102)
                ->order(['create_time' => 'desc'])
                ->find();
            if($room){
                $room->save([
                    'live_status' => 101,
                    'is_notice' => 0, //预告状态
                    'real_start_time' => time(), //实际开始时间
                ]);
            }
            return true;
        }
        return false;
    }

     /**
      * 设置为停止推流
      */
    public function setStop($data){
        //设置最新一个房间为已开始
        $room_id = explode('_', $data['channel_id']);
        $room = $this->where('qcloud_room_id', '=', 'room_'.$room_id[1])
            ->where('live_status', '=', 101)
            ->order(['create_time' => 'desc'])
            ->find();
        if($room){
            $data = [
                'live_status' => 103,
                'is_notice' => 0,
                'real_end_time' => time(), //实际结束时间
            ];
            // 小于1分钟删除
            if($data['real_end_time'] - $room['real_start_time'] < 60){
                $data['is_delete'] = 1;
            }
            $room->save($data);
        }
        return true;
    }

    /**
     * 回放地址
     * {"app":"59302.livepush.myqcloud.com","appid":1251815325,"appname":"live","channel_id":"1400410245_10002",
     * "duration":107,"end_time":1598454291,"end_time_usec":655664,"event_type":100,"file_format":"mp4",
     * "file_id":"5285890806906490148","file_size":6847302,"media_start_time":3151,"record_bps":0,
     * "record_file_id":"5285890806906490148","sign":"316dce02ade926ffbdeae56dac1eb764","start_time":1598454185,
     * "start_time_usec":991166,"stream_id":"1400410245_10002",
     * "stream_param":"txSecret=c7606f1568c4639375e908bf4bf16a5d&txTime=5F47CB23","t":1598454893,
     * "task_id":"1818336333418013137","video_id":"1251815325_6b522017b15f4095943d1ef38a182e37","
     * video_url":"http://1251815325.vod2.myqcloud.com/0c23ab63vodcq1251815325/49a263515285890806906490148/f0.mp4"}
     */
    public function setRecord($data){
        //设置最新一个房间为已开始
        $room_id = explode('_', $data['channel_id']);
        $room = $this->where('qcloud_room_id', '=', 'room_'.$room_id[1])
            ->where('live_status', '=', 103)
            ->order(['create_time' => 'desc'])
            ->find();
        if($room){
            $room->save([
                'video_url' => $data['video_url']
            ]);
            return true;
        }
        return false;
    }


    /**
     * 获取过期的预告，超时一小时
     */
    public function getEndList()
    {
        return $this->where('start_time', '<=', time() - 60 * 60)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 设置预告为过期
     */
    public function setIsEnd($roomIds)
    {
        return $this->where('room_id' , 'in', $roomIds)->data([
            'live_status' => 107
        ])->update();
    }

    /**
     * 获取过期的直播,5分钟未推流
     */
    public function getEndRoomList()
    {
        return $this->where('real_end_time', '<=', time() - 300)
            ->where('live_status', '=', 101)
            ->where('is_delete', '=', 0)
            ->select();
    }


    /**
     * 设置直播状态为已结束
     */
    public function setIsRoomEnd($roomIds)
    {
        return $this->where('room_id' , 'in', $roomIds)->data([
            'live_status' => 103,
            'is_notice' => 0
        ])->update();
    }
}