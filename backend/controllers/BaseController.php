<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/15
 * Time: 16:37
 */

namespace backend\controllers;

use Yii;
use common\models\AdminRole;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;

class BaseController extends Controller
{
    /**
     * 验证权限
     * @param \yii\base\Action $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        if(Yii::$app->session->get('ROLE_ID')) {
            header("location:".Url::to(['site/login'], true));
            die;
        }
        $adminRole = AdminRole::findOne(Yii::$app->session->get('ROLE_ID'));
        $permissions = json_decode($adminRole->permissions, true);
        $urlArr = ArrayHelper::getColumn($permissions, 'url');
        $url = '/'.$action->controller->id.'/'.$action->id;
        if($action->id == 'index') {
            $url_one = '/'.$action->controller->id;
            
            if(!in_array($url, $urlArr) && !in_array($url_one, $urlArr)) {
                throw new HttpException(403);
            }
        } else {
            if(!in_array($url, $urlArr)) {
                throw new HttpException(403);
            }
        }
        
        return true;
    }
}