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
        'UserVoucher' => [
            \app\job\event\UserCoupon::class
        ],

    ],

    'subscribe' => [
    ], 
];
