<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '192.168.1.87',
            'port' => 6379,
            'password' => 'redis_6379',
        ],
        'queue' => [
            'class' => 'zhuravljov\yii\queue\redis\Queue',
            'redis' => 'redis', // connection ID
            'channel' => 'queue', // queue channel
        ],
        'queue_second' => [
            'class' => 'zhuravljov\yii\queue\redis\Queue',
            'redis' => 'redis', // connection ID
            'channel' => 'queue_second', // queue channel
        ],
    ],
];
