<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/4
 * Time: 下午3:09
 */

namespace passport\modules\inside\controllers;


use passport\modules\inside\models\PreOrder;
use passport\modules\inside\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class PreOrderController extends BaseController
{
    /**
     * 获取单挑交易记录，通过订单号
     * @param $order_id
     * @return array
     */
    public function actionInfo($order_id)
    {
        $model = PreOrder::find()->where(['order_id' => $order_id])->one();

        if ($model) {
            return $this->_return($model);
        } else {
            return $this->_return(null, 2005, '该订单不存在');
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
        $pageSize = Yii::$app->request->get('page-size', 20);

        $query = PreOrder::find();
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
            'status' => PreOrder::STATUS_PENDING
        ]);

        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
        $pagination = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageSize]);

        return $this->_return([
            'list' => $data->getModels(),
            'pages' => [
                'totalCount' => intval($pagination->totalCount),
                'pageCount' => $pagination->pageCount,
                'currentPage' => $pagination->page + 1,
                'perPage' => $pagination->pageSize,
            ]
        ]);
    }
}