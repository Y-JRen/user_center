<?php

namespace passport\modules\inside\controllers;


use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;

class BaseController extends \passport\controllers\BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['http://user.admin.che.com'],
                ]
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => $this->verbs(),
            ],
        ];
    }
}
