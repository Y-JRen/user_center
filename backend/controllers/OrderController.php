<?php

namespace backend\controllers;

use common\models\LogReview;
use Yii;
use common\models\Order;
use backend\models\search\OrderSearch;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends BaseController
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
        ];
    }
    
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 线下充值待确认
     * @return mixed
     */
    public function actionLineDown()
    {
        $queryParams['OrderSearch'] = ['order_type' => Order::TYPE_RECHARGE, 'order_subtype' => 'line_down', 'status' => Order::STATUS_PROCESSING];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 完成财务确认动作
     * @param $id
     * @return string
     * @throws ErrorException
     */
    public function actionViewLineDown($id)
    {
        $model = $this->findModel($id);

        if ($model->isEdit && Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $logModel = new LogReview(['order_id'=>$id]);
                $logModel->load(Yii::$app->request->post());
                if (!$logModel->save()) {
                    throw new ErrorException(print_r($logModel->errors, true));
                }

                $model->status = $logModel->order_status;
                if (!$model->save()) {
                    throw new ErrorException('更新订单状态失败');
                }

                if ($model->isSuccessful) {
                    if (!$model->userBalance->plus($model->amount)) {
                        throw new ErrorException('更新用户余额失败');
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (ErrorException $e) {
                Yii::$app->session->setFlash('error', '处理失败');
                $transaction->rollBack();
                throw $e;
            }
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * 提现待确认
     * @return mixed
     */
    public function actionCash()
    {
        $queryParams['OrderSearch'] = ['order_type' => Order::TYPE_CASH, 'status' => Order::STATUS_PROCESSING];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 完成财务提现确认动作
     * @param $id
     * @return string
     * @throws ErrorException
     */
    public function actionViewCash($id)
    {
        $model = $this->findModel($id);

        if ($model->isEdit && Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $logModel = new LogReview(['order_id'=>$id]);
                $logModel->load(Yii::$app->request->post());
                if (!$logModel->save()) {
                    throw new ErrorException(print_r($logModel->errors, true));
                }

                $model->status = $logModel->order_status;
                if (!$model->save()) {
                    throw new ErrorException('更新订单状态失败');
                }

                if ($model->isSuccessful) {
                    if (!$model->userFreeze->less($model->amount)) {
                        throw new ErrorException('更新用户冻结余额失败');
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', '处理成功');
            } catch (ErrorException $e) {
                Yii::$app->session->setFlash('error', '处理失败');
                $transaction->rollBack();
                throw $e;
            }
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
