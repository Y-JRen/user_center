<?php

namespace backend\controllers;

use common\models\CompanyAccount;
use common\models\LogReview;
use common\models\RechargeConfirm;
use common\models\User;
use Yii;
use common\models\Order;
use backend\models\search\OrderSearch;
use yii\base\ErrorException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

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
        $queryParams['OrderSearch'] = [
            'order_type' => Order::TYPE_RECHARGE,
            'order_subtype' => 'line_down',
            'status' => Order::STATUS_PROCESSING
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);
        
        return $this->render('line-down', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionLineDownForm($id)
    {
        /**
         * @var
         */
        $model = $this->findModel($id);
        $phone = \common\models\User::findOne($model->uid)->phone;
        $remark = json_decode($model->remark, true);
        $info = '';
        if (is_array($remark)) {
            foreach ($remark as $key => $value) {
                $info .= "<p>$key : $value</p>";
            }
        } else {
            $info = '<p>银行账号信息不健全</p>';
        }
        $dropList = '';
        foreach (CompanyAccount::dropList(3) as $k => $v) {
            $dropList .= '<option value=' . $k . '>' . $v . '</option>';
        }
        $html = <<<_HTML
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">充值确认</h4>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
        <tr>
          <td>充值单号</td>
          <td>{$model->order_id}</td>
          <td>用户</td>
          <td>{$phone}</td>
        </tr>
        <tr>
          <td>充值金额</td>
          <td>{$model->amount}</td>
          <td>充值方式</td>
          <td>线下支付</td>
        </tr>
      </tbody>
    </table>
        <div class="callout callout-info lead">
            <h4>银行账号信息</h4>
            {$info}
        </div>
</div>
<form class="form-horizontal" action="/order/line-down-save?id={$model->id}" method="post">
    <div class="box-body">
    <input type="hidden" class="form-control" value="{$model->id}" name='id'>
    <div class="form-group">
      <label class="col-sm-2 control-label">账号</label>
      <div class="col-sm-10">
        <select name='account_id' class="form-control" id="inputEmail3">
        {$dropList}
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">流水号</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name='back_order'>
      </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" id="line-down-save" class="btn btn-primary">确认到账</button>
    </div>
</form>
_HTML;
        return $html;
        
    }
    
    public function actionLineDownSave()
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($post['id']);
        if ($model->status !== $model::STATUS_PROCESSING) {
            return json_encode([
                'code' => -1,
                'message' => '错误请求'
            ]);
        }
        $db = Yii::$app->db->beginTransaction();
        try {
            $recharge = new RechargeConfirm();
            $recharge->order_id = $model->id;
            $recharge->account_id = $post['account_id'];
            $companyAccount = CompanyAccount::findOne($post['account_id']);
            $recharge->account = $companyAccount->bank_name . '-' . $companyAccount->card_bumber;
            $recharge->back_order = $post['back_order'];
            $recharge->created_at = time();
            if (!$recharge->save()) {
                throw new Exception('确认失败', $recharge->errors);
            }
    
            $model->userBalance->plus($model->amount);
            $model->setOrderSuccess();
            $db->commit();
            Yii::$app->session->setFlash('success', '确认成功');
        } catch (Exception $e) {
            $db->rollBack();
            Yii::$app->session->setFlash('error', '确认失败');
        }
        
        return $this->redirect(['line-down']);
    }
    
    /**
     * 线下充值待确认
     * @return mixed
     */
    public function actionLineDownLog()
    {
        $queryParams['OrderSearch'] = [
            'order_type' => Order::TYPE_RECHARGE,
            'order_subtype' => "line_down",
            'status' => [Order::STATUS_SUCCESSFUL, Order::STATUS_FAILED]
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);
        
        return $this->render('line-down-log', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionUserDetail($uid)
    {
        $user = User::findOne($uid);
        $queryParams['OrderSearch'] = [
            'status' => Order::STATUS_SUCCESSFUL,
            'uid' => $uid
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($queryParams);
        return $this->render('user-detail', [
            'dataProvider' => $dataProvider,
            'userModel' => $user
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
                $logModel = new LogReview(['order_id' => $id]);
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
                $logModel = new LogReview(['order_id' => $id]);
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
