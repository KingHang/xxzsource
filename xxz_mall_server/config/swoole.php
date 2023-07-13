<?php

use app\swoole\listener\Message;
use app\swoole\Handler;
use app\swoole\Parser;
use \Swoole\Table;
return [
    'server'     => [
        'host'      => env('SWOOLE_HOST', '127.0.0.1'), // 监听地址
        'port'      => env('SWOOLE_PORT', 9501), // 监听端口
        'mode'      => SWOOLE_PROCESS, // 运行模式 默认为SWOOLE_PROCESS
        'sock_type' => SWOOLE_SOCK_TCP, // sock type 默认为SWOOLE_SOCK_TCP
        'options'   => [
            'pid_file'              => runtime_path() . 'swoole.pid',
            'log_file'              => runtime_path() . 'swoole.log',
            'daemonize'             => true,
            // Normally this value should be 1~4 times larger according to your cpu cores.
            'reactor_num'           => 1,
            'worker_num'            => 1,
            'task_worker_num'       => 1,
            'task_enable_coroutine' => true,
            'task_max_request'      => 3000,
            'enable_static_handler' => true,
            'document_root'         => root_path('public'),
            'package_max_length'    => 20 * 1024 * 1024,
            'buffer_output_size'    => 10 * 1024 * 1024,
            'socket_buffer_size'    => 128 * 1024 * 1024,
            'max_request'           => 3000,
            'send_yield'            => true,
        ],
    ],
    'websocket'  => [
        'enable'        => true,
        'handler'       => Handler::class,
        'parser'        => Parser::class,
        'ping_interval' => 25000,
        'ping_timeout'  => 60000,
        'room'          => [
            'type'  => 'table',
            'table' => [
                'room_rows'   => 4096,
                'room_size'   => 2048,
                'client_rows' => 8192,
                'client_size' => 2048,
            ],
            'redis' => [

            ],
        ],
        'listen'        => [
            'message'  => Message::class
        ],
        'subscribe'     => [],
    ],
    'rpc'        => [
        'server' => [
            'enable'   => false,
            'port'     => 9000,
            'services' => [],
        ],
        'client' => [
            'Test'=>[
                'host'=>'127.0.0.1',
                'port'=>9000,
            ],
        ],
    ],
    'hot_update' => [
        'enable'  => true,
        'name'    => ['*.php'],
        'include' => [app_path()],
        'exclude' => [],
    ],
    //连接池
    'pool'       => [
        'db'    => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
        'cache' => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
    ],
    'coroutine'  => [
        'enable' => true,
        'flags'  => SWOOLE_HOOK_ALL,
    ],
    'tables'     => [
        'u2fd' => [
            'size' => 10240,
            'columns' => [
                ['name' => 'fd', 'type' => 1, 'size' => 8]
            ]
        ],
        'fd2u' => [
            'size' => 10240,
            'columns' => [
                ['name' => 'sid', 'type' => 1, 'size' => 8]
            ]
        ],
        's2fd' => [
            'size' => 10240,
            'columns' => [
                ['name' => 'fd', 'type' => 1, 'size' => 8]
            ]
        ],
        'fd2s' => [
            'size' => 10240,
            'columns' => [
                ['name' => 'sid', 'type' => 1, 'size' => 8]
            ]
        ]
    ],
    //每个worker里需要预加载以共用的实例
    'concretes'  => [],
    //重置器
    'resetters'  => [],
    //每次请求前需要清空的实例
    'instances'  => [],
    //每次请求前需要重新执行的服务
    'services'   => [],
];
