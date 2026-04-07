<?php

// +----------------------------------------------------------------------
// |  HlwAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2024 hlw2326
// +----------------------------------------------------------------------
// | 邮箱: 1608626143@qq.com
// | 官方网站: https://www.hlw2326.com
// +----------------------------------------------------------------------

return [
    // 服务监听地址
    'host' => '127.0.0.1',
    // 服务监听端口
    'port' => 2346,
    // 套接字上下文选项
    'context' => [],
    // 高级自定义服务类
    'classes' => '',
    // 消息请求回调处理
    'callable' => null,
    // 服务进程参数配置
    'worker' => [
        // 进程名称
        "name" => 'ThinkAdmin',
        // 进程数量
        'count' => 2,
    ],
    // 监控文件变更重载
    'files' => [
        // 监控检测间隔（单位秒，零不监控）
        'time' => 60,
        // 文件监控目录（默认监控 app+config 目录）
        'path' => [],
        // 文件监控后缀（默认监控 所有 文件）
        'exts' => ['*.php']
    ],
    // 监控内存超限重载
    'memory' => [
        // 监控检测间隔（单位秒，零不监控）
        'time' => 60,
        // 限制内存大小（可选单位有 G M K ）
        'limit' => '1G'
    ],
    // 自定义服务配置（可选）
    'customs' => [
        // 自定义 text 服务
        'text' => [
            // 进程类型(Workerman|Gateway|Register|Business)
            'type' => 'Workerman',
            // 监听地址(<协议>://<地址>:<端口>)
            'listen' => 'text://0.0.0.0:8685',
            // 高级自定义服务类
            'classes' => '',
            // 套接字上下文选项
            'context' => [],
            // 服务进程参数配置
            'worker' => [
                //'name' => 'TextTest',
                // onWorkerStart => [class,method]
                // onWorkerReload => [class,method]
                // onConnect => [class,method]
                // onBufferFull => [class,method]
                // onBufferDrain => [class,method]
                // onError => [class,method]
                // 设置连接的 onMessage 回调
                'onMessage' => function ($connection, $data) {
                    $connection->send("hello world");
                }
            ]
        ],
        // 自定义 websocket 服务
        'websocket' => [
            // 进程类型(Workerman|Gateway|Register|Business)
            'type' => 'Workerman',
            // 监听地址(<协议>://<地址>:<端口>)
            'listen' => 'websocket://0.0.0.0:8686',
            // 高级自定义服务类
            'classes' => '',
            // 套接字上下文选项
            'context' => [],
            // 服务进程参数配置
            'worker' => [
                //'name' => 'TextTest',
                // onWorkerStart => [class,method]
                // onWorkerReload => [class,method]
                // onConnect => [class,method]
                // onBufferFull => [class,method]
                // onBufferDrain => [class,method]
                // onError => [class,method]
                // 设置连接的 onMessage 回调
                'onMessage' => function ($connection, $data) {
                    $connection->send("hello world");
                }
            ]
        ],
        // 自定义 Gateway 服务
        'gateway' => [
            // 进程类型(Workerman|Gateway|Register|Business)
            'type' => 'Gateway',
            // 监听地址(<协议>://<地址>:<端口>)
            'listen' => 'websocket://127.0.0.1:8689',
            // 高级自定义服务类
            'classes' => '',
            // 套接字上下文选项
            'context' => [],
            // 服务进程参数配置
            'worker' => [
                // 进程名称
                "name" => 'Gateway',
                // 进程数量
                 "count" => 4,
                // 心跳发送时间，针对客户端
                'pingInterval' => 10,
                // 心跳容错次数，针对客户端
                'pingNotResponseLimit' => 0,
                // 心跳包内容，针对客户端
                'pingData' => '{"type":"ping"}',
                 // 服务器内网IP
                "lanIp" => '127.0.0.1',
                // Business 回复 Gateway 端口
                "startPort" => 2000,
               // 注册服务地址，与 Register 进程对应
                "registerAddress" => '127.0.0.1:1236',
                // 进程启动回调
                "onWorkerStart" => function () {
                    echo "Gateway onWorkerStart" . PHP_EOL;
                },
                 // 进程停止回调
                "onWorkerStop" => function () {
                    echo "Gateway onWorkerStop" . PHP_EOL;
                }
            ]
        ],
        // 自定义 Register 服务
        'register' => [
            // 进程类型(Workerman|Gateway|Register|Business)
            'type' => 'Register',
            // 监听地址(<协议>://<地址>:<端口>)
            // 注意：别改这里的协议，只支持 text 协议
            'listen' => 'text://127.0.0.1:1236'
        ],
        // 自定义 Business 服务
        'business' => [
            // 进程类型(Workerman|Gateway|Register|Business)
            'type' => 'Business',
            // 高级自定义服务类
            'classes' => '',
            // 服务进程参数配置
            'worker' => [
                // 进程名称
                "name" => 'Business',
                // 进程数量
                "count" => 4,
                // 注册服务地址，与 Register 进程对应
                "registerAddress" => '127.0.0.1:1236',
                // 业务处理类
                "eventHandler" => Events::class,
                // 进程启动回调
                "onWorkerStart" => function () {
                    echo "Business onWorkerStart" . PHP_EOL;
                },
                // 进程停止回调
                "onWorkerStop" => function () {
                    echo "Business onWorkerStart" . PHP_EOL;
                }
            ]
        ],
    ],
];

/**
 * 业务处理类
 * @class Events
 */
class Events
{

    /**
     * 业务进程启动
     * @param $businessWorker
     * @return void
     */
    public static function onWorkerStart($businessWorker)
    {
        echo "Events WorkerStart\n";
    }

    /**
     * 有消息时触发该方法
     * @param int $clientid 发消息的client_id
     * @param mixed $message 消息
     * @throws \Exception
     */
    public static function onMessage($clientid, $message)
    {
        // 群聊，转发请求给其它所有的客户端
        \GatewayWorker\Lib\Gateway::sendToAll("Message By Events : {$message}");
    }
}
