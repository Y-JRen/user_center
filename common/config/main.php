<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\redis\cache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'REDIS',
            'port' => 6379,
            'database' => 0,
        ],

    ],
];
