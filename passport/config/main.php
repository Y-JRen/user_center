<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 10:40
 */

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);


return [
    'id' => 'passport',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'passport\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-passport',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $response->data = [
                    'success' => $response->isSuccessful,
                    'data' => $response->data,
                ];
            },
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-passport', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-passport',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION']
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];