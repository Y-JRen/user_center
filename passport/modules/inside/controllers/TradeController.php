<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/15
 * Time: 17:21
 */

namespace passport\modules\inside\controllers;


use Yii;
use passport\controllers\BaseController;
use passport\modules\inside\models\Order;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class TradeController extends BaseController
{
    /**
     * 获取用户的交易记录
     * @param $uid
     * @return array
     */
    public function actionList($uid)
    {

        $query = Order::find()->where([
            'uid' => $uid
        ]);

        $orderType = Yii::$app->request->get('order_type');
        if ($orderType) {
            $query->andWhere(['order_type' => $orderType]);
        }
        $status = Yii::$app->request->get('status');
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
}