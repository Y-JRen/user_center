<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/9
 * Time: 10:28
 */

namespace passport\modules\pay\controllers;


use passport\modules\pay\models\OrderForm;
use Yii;
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
    public function verbs()
    {
        return [
            'list' => ['get'],
            'info' => ['get'],
        ];
    }

    /**
     * 列表
     *
     * @return array
     */
    public function actionList()
    {
        $query = OrderForm::find()->where([
            'platform' => Config::getPlatform(),
            'uid' => Yii::$app->user->getId()
        ])->orderBy('id desc');

        $orderType = \Yii::$app->request->get('order_type');
        if ($orderType) {
            $query->andWhere(['order_type' => $orderType]);
        }
        $status = \Yii::$app->request->get('status');
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
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

    /**
     * 获取订单详情
     *
     * @param $order_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionInfo($order_id)
    {
        $result = OrderForm::find()->where(['order_id' => $order_id, 'uid' => Yii::$app->user->id])->one();
        return $this->_return($result);
    }
}