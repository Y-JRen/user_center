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
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use backend\logic\ThirdLogic;


/**
 * 基础控制器
 *
 * Class BaseController
 * @package backend\controllers
 */
class BaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => $this->verbs(),
            ],
        ];
    }

    public function verbs()
    {
        return [];
    }

    /**
     * 验证权限
     * @param \yii\base\Action $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->session->get('ROLE_ID')) {
            header("location:" . Url::to(['site/login'], true));
            die;
        }
        $adminRole = AdminRole::findOne(Yii::$app->session->get('ROLE_ID'));
        $permissions = json_decode($adminRole->permissions, true);
        $urlArr = ArrayHelper::getColumn($permissions, 'url');
        $allMenu = ArrayHelper::getColumn(ThirdLogic::instance()->getPermission(), 'url');

        $url = '/' . $action->controller->id . '/' . $action->id;
        if ($action->id == 'index') {
            $url_one = '/' . $action->controller->id;

            if (!in_array($url, $urlArr) && !in_array($url_one, $urlArr) && in_array($url, $allMenu)) {
                throw new HttpException(403);
            }
        } else {
            if (!in_array($url, $urlArr) && in_array($url, $allMenu)) {
                throw new HttpException(403);
            }
        }

        return true;
    }
}