<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/21
 * Time: 下午1:54
 */

namespace backend\controllers;

use common\helpers\ConfigHelper;
use common\models\User;
use Yii;
use backend\models\Order;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * 资金核销
 * Class WriteOffController
 * @package backend\controllers
 */
class WriteOffController extends BaseController
{
    public function actionIndex()
    {
        $query = Order::find()->where(['order_subtype' => Order::SUB_TYPE_WRITE_OFF]);

        $query->andFilterWhere(['order_type' => Yii::$app->request->get('order_type')]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider,]);
    }

    public function actionCreate()
    {
        $model = new Order();
        $model->setScenario(Order::SCENARIO_WRITE_OFF);
        $model->order_subtype = Order::SUB_TYPE_WRITE_OFF;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $user = User::find()->where(['phone' => $model->phone])->one();
            $model->uid = ArrayHelper::getValue($user, 'id');
            $model->platform = 6;
            $model->order_id = ConfigHelper::createOrderId();
            $model->status = Order::STATUS_SUCCESSFUL;
            $model->notice_status = 4;

            $db = Yii::$app->db->beginTransaction();

            if ($model->save()) {

                if ($model->order_type == Order::TYPE_REFUND)//退款
                {
                    $status = $model->userBalance->addMoney($model->amount, $model);
                } else {// 消费
                    $status = $model->userBalance->cutMoney($model->amount, $model);
                }

                if ($status) {
                    $db->commit();

                    $model->addLogReview('添加核销记录');
                    Yii::$app->session->setFlash('success', '添加核销记录成功');
                }
            } else {
                var_dump($model->errors);
                exit;
            }

            $db->rollBack();
        }

        return $this->renderPartial('create', ['model' => $model]);
    }
}