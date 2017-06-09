<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/9
 * Time: 10:28
 */

namespace passport\modules\pay\controllers;


use common\models\Order;
use passport\controllers\AuthController;
use passport\helpers\Config;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

/**
 * 消费记录
 *
 *
 * Class TradeController
 * @package passport\modules\pay\controllers
 */
class TradeController extends AuthController
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