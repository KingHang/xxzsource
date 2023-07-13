<?php
// 事件定义文件
return [
    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'PaySuccess' => [
            \app\api\event\PaySuccess::class
        ],
        /*用户等级*/
        'UserGrade' => [
            \app\job\event\UserGrade::class
        ],
        /*用户等级*/
        'AgentUserGrade' => [
            \app\job\event\AgentUserGrade::class
        ],
        /*任务调度*/
        'JobScheduler' => [
            \app\job\event\JobScheduler::class
        ],
        /*订单事件*/
        'Order' => [
            \app\job\event\Order::class
        ],
        /*拼团订单*/
        'AssembleBill' => [
            \app\job\event\AssembleBill::class
        ],
        /*砍价任务*/
        'BargainTask' => [
            \app\job\event\BargainTask::class
        ],
        /*领取优惠券事件*/
        'UserCoupon' => [
            \app\job\event\UserCoupon::class
        ],
        /*分销商订单*/
        'AgentOrder' => [
            \app\job\event\AgentOrder::class
        ],
        /*直播间管理*/
        'LiveRoom' => [
            \app\job\event\LiveRoom::class
        ],
        /*分佣订单月结*/
        'AgentOrderMonth' => [
            \app\job\event\AgentOrderMonth::class
        ],
        /*积分年度分红*/
        'AgentOrderPoints' => [
            \app\job\event\AgentOrderPoints::class
        ],
        /*人脸app订单支付 成功事件*/
        'FacePaySuccess' => [
            \app\faceRecognition\event\PaySuccess::class
        ],
        /*会员注册成功事件*/
        'UserRegister' => [
           \app\common\event\UserRegister::class
        ],
        /*会员注册成功事件*/
        'UserUpdate' => [
            \app\common\event\UserUpdate::class
        ],
        /*活动报名提醒*/
        'Activity' => [
            \app\job\event\Activity::class
        ],
    ],

    'subscribe' => [
    ],
];
