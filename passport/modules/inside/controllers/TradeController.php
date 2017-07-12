<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/15
 * Time: 17:21
 */

namespace passport\modules\inside\controllers;


use passport\modules\inside\models\User;
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
        ])->orderBy('id desc');

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

    /**
     * 获取单挑交易记录，通过订单号
     * @param $order_id
     * @return array
     */
    public function actionInfo($order_id)
    {
        $model = Order::find()->where(['order_id' => $order_id])->one();
        if ($model) {
            return $this->_return($model);
        } else {
            return $this->_error(2005, '改订单不存在');
        }
    }


    /**
     * 通过手机号跟订单号搜索
     * @param $key
     * @return array
     */
    public function actionSearch($key)
    {
        $sort = Yii::$app->request->get('sort', 'desc');

        $query = Order::find()->orderBy("id {$sort}");
        if (strlen($key) > 11)// 订单号
        {
            $query->where(['order_id' => $key]);
        } else {// 手机号
            $user = User::find()->where(['phone' => $key])->asArray()->one();
            if ($user) {
                $query->where(['uid' => $user['id']]);
            } else {
                return $this->_return(null, 0, '暂无相关数据');
            }
        }

        $type = Yii::$app->request->get('type');
        if (!empty($type)) {
            $query->andFilterWhere(['order_type' => $type]);
        }

        $subtype = Yii::$app->request->get('subtype');
        if (!empty($subtype)) {
            $query->andFilterWhere(['order_subtype' => $subtype]);
        }

        $status = Yii::$app->request->get('status');
        if (!empty($status)) {
            $query->andFilterWhere(['status' => $status]);
        }

        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('page_size', 20),
            ],
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