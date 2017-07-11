<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/22
 * Time: 1:28
 */

namespace common\jobs;

use common\logic\CrmLogic;
use common\models\Order;
use common\models\RechargeConfirm;
use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use zhuravljov\yii\queue\Job;

class RechargePushJob extends Object implements Job
{
    public $back_order;//银行流水号
    public $amount;//金额
    public $order_id;//订单id
    public $transaction_time;//到账时间
    public $method;//充值方式
    public $uid;// 客户id

    public function execute($queue)
    {
        $order = Order::find()->where(['order_id' => $this->order_id])->asArray()->one();
        $status = 1;
        switch ($this->method) {
            case 1;
                $alipay = Yii::$app->params['recharge_push']['alipay'];
                $subtype = ArrayHelper::getValue($order, 'order_subtype');
                $key = (($subtype == 'tmall') ? 'app@che.com' : 'default');
                $config = ArrayHelper::getValue($alipay, $key);
                break;
            case 2:
                $config = Yii::$app->params['recharge_push']['wechat'];
                break;
            case 3:
                $config = Yii::$app->params['recharge_push']['lakala'];
                break;
            default:
                echo '充值方式错误';
                Yii::$app->end();
        }

        $orgInfo = CrmLogic::instance()->getOrgInfo($this->uid);
        if ($orgInfo) {
            $org_id = ArrayHelper::getValue($orgInfo, 'shop_id');
            $org = ArrayHelper::getValue($orgInfo, 'shop_name');
            $remark = '';
        } else {
            $org_id = 0;
            $org = '获取组织信息失败';
            $remark = '获取组织信息失败';
            $status = 2;
        }

        $model = new RechargeConfirm();
        $model->order_id = $this->order_id;
        $model->method = $this->method;
        $model->transaction_time = strtotime($this->transaction_time);
        $model->amount = $this->amount;
        $model->back_order = $this->back_order;
        $model->org_id = $org_id;
        $model->org = $org;
        $model->account_id = $config['account_id'];
        $model->account = $config['account'];
        $model->type_id = $config['type_id'];
        $model->type = $config['type'];
        $model->remark = $remark;
        $model->status = $status;
        $model->created_at = time();
        if (!$model->save()) {
            echo '充值记录添加失败' . json_encode($model->errors);
        }
    }
}