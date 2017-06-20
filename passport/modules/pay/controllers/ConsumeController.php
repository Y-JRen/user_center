<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/8
 * Time: 17:29
 */

namespace passport\modules\pay\controllers;


use common\models\Order;
use passport\controllers\AuthController;
use passport\helpers\Config;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;


/**
 * Class ConsumeController
 * @package passport\modules\pay\controllers
 */
class ConsumeController extends AuthController
{
    /**
     * 列表
     *
     * @return array
     */
    public function actionList()
    {
        $query = Order::find()->where([
            'platform' => Config::getPlatform(),
            'uid' => \Yii::$app->user->getId()
        ]);
        $data = new ActiveDataProvider([
            'query' => $query,
        ]);
        $pagination = new Pagination(['totalCount' => $query->count()]);

        return $this->_return([
            'list' => $data->getModels(),
            'pages' => [
                'totalCount' => intval($pagination->totalCount),
                'pageCount' => $pagination->getPageCount(),
                'currentPage' => $pagination->getPage() + 1,
                'perPage' => $pagination->getPageSize(),
            ]
        ]);
    }
}