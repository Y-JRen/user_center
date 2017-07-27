<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/15
 * Time: 17:21
 */

namespace passport\modules\inside\controllers;


use passport\modules\inside\models\User;
use passport\modules\pay\models\OrderForm;
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
        $status = Yii::$app->request->get('status', Order::$defaultSearchStatus);

        $query->andFilterWhere([
            'order_type' => $orderType,
            'status' => $status
        ]);
        $data = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
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
        $key = strtoupper($key);
        $sort = Yii::$app->request->get('sort', 'DESC');

        if (!in_array(strtoupper($sort), ['ASC', 'DESC'])) {
            $sort = 'DESC';
        }

        $query = Order::find()->orderBy("id {$sort}");
        $length = strlen($key);
        switch ($length) {
            case 11://手机号
                $user = User::find()->where(['phone' => $key])->asArray()->one();
                if ($user) {
                    $query->where(['uid' => $user['id']]);
                } else {
                    return $this->_return(null, 0, '暂无相关数据');
                }
                break;
            case 19://用户中心单号
                $query->where(['order_id' => $key]);
                break;
            case 18://电商订单号
                $query->where(['platform_order_id' => $key]);
                break;
            default:
                return $this->_return(null, 0, '传递的查询单号不正确');
        }

        $query->andFilterWhere([
            'order_type' => Yii::$app->request->get('type'),
            'order_subtype' => Yii::$app->request->get('subtype'),
            'status' => Yii::$app->request->get('status')
        ]);

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