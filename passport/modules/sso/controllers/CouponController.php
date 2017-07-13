<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/4
 * Time: 11:15
 */

namespace passport\modules\sso\controllers;


use passport\modules\sso\models\Coupon;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class CouponController extends BaseController
{
    /**
     * Lists all CarManagement models.
     * @return mixed
     */
    public function actionList()
    {
        $query = Coupon::find();

        $status = Yii::$app->request->get('status');
        if (!empty($status)) {
            $query->andFilterWhere(['status' => $status]);
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
     * Get a single Coupon model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->_return($this->findModel($id));
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    protected function findModel($id)
    {
        $model = Coupon::find()->where(['id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new InvalidParamException('传递参数有误', 1101);
        }
    }
}