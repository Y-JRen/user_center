<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 11:22
 */

namespace passport\controllers;


use common\auth\BaseAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\web\Response;

class AuthController extends BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => $this->verbs(),
            ],
            'basicAuth' => [
                'class' => BaseAuth::className(),
                'tokenParam ' => 'userToken'
            ],
            'rateLimiter' => [
                'class' => RateLimiter::className(),
            ],
        ];
    }
}