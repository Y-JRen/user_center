<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 10:39
 */

namespace passport\controllers;


use yii\rest\Controller;


/**
 * Class BaseController
 * @package passport\controllers
 */
class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'passport\actions\RestErrorAction',
            ],
        ];
    }

    public function actionError()
    {

    }



}