<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 10:40
 */

use yii\helpers\ArrayHelper;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);


return [
    'id' => 'passport',
    'language' => 'zh-CN',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'passport\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'sso' => [
            'class' => 'passport\modules\sso\Module'
        ],
        'pay' => [
            'class' => 'passport\modules\pay\Module'
        ],
        'inside' => [
            'class' => 'passport\modules\inside\Module',
        ],
    ],
    'components' => [
        'formatter' => [
            'datetimeFormat' => 'php:Y-m-d H:i',
            'currencyCode' => 'CNY',
        ],
        'request' => [
            'csrfParam' => '_csrf-passport',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    if ($response->isSuccessful) {
                        $response->data = [
                            'message' => ArrayHelper::getValue($response->data, 'message', ''),
                            'err_code' => intval(ArrayHelper::getValue($response->data, 'code', 0)),
                            'data' => ArrayHelper::getValue($response->data, 'data'),
                        ];
                    } else {
                        $code = intval(Yii::$app->errorHandler->exception->getCode());
                        $code = empty($code) ? $response->statusCode : $code;// 返回码为0时，使用状态码
                        $response->data = [
                            'message' => Yii::$app->errorHandler->exception->getMessage(),
                            'err_code' => $code,
                            'data' => Yii::$app->params['orderErr'],
                        ];
                    }
                    $response->statusCode = 200;
                }
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