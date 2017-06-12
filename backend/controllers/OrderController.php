<?php

namespace backend\controllers;

use common\models\LogReview;
use Yii;
use common\models\Order;
use backend\models\search\OrderSearch;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
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

        return $this->render('line_down', [
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

        return $this->render('line_down_view', [
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
     * 财务退款操作
     * @return mixed
     */
    public function actionRefund()
    {
        $queryParams['OrderSearch'] = ['order_type' => Order::TYPE_REFUND];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 贷款进账
     * @return string
     */
    public function actionLoan()
    {
        $queryParams['OrderSearch'] = ['order_type' => Order::TYPE_REFUND];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
