<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/22
 * Time: 1:28
 */

namespace common\jobs;

use common\models\RechargeConfirm;
use Yii;
use yii\base\Object;
use zhuravljov\yii\queue\Job;

class RechargePushJob extends Object implements Job
{
    public $back_order;//银行流水号
    public $amount;//金额
    public $order_id;//订单id
    public $transaction_time;//到账时间
    public $method;//充值方式

    public function execute($queue)
    {
        switch ($this->method) {
            case 1;
                $config = Yii::$app->params['recharge_push']['alipay'];
                break;
            case 2:
                $config = Yii::$app->params['recharge_push']['wechat'];
                break;
            default:
                echo '充值方式错误';
                Yii::$app->end();
        }

        $model = new RechargeConfirm();
        $model->order_id = $this->order_id;
        $model->method = $this->method;
        $model->transaction_time = $this->transaction_time;
        $model->amount = $this->amount;
        $model->back_order = $this->back_order;
        $model->org_id = $config['org_id'];
        $model->org = $config['org'];
        $model->account_id = $config['account_id'];
        $model->account = $config['account'];
        $model->type_id = $config['type_id'];
        $model->type = $config['type'];
        $model->created_at = time();
        if (!$model->save()) {
            echo '充值记录添加失败' . json_encode($model->errors);
        }
    }
}