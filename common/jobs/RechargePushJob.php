<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/22
 * Time: 1:28
 */

namespace common\jobs;

use common\logic\CrmLogic;
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

        $orgInfo = CrmLogic::instance()->getOrgInfo($this->uid);
        if ($orgInfo) {
            $org_id = ArrayHelper::getValue($orgInfo, 'shop_id');
            $org = ArrayHelper::getValue($orgInfo, 'shop_name');
            $salesman_id = ArrayHelper::getValue($orgInfo, 'salesman_id');
            $salesman_name = ArrayHelper::getValue($orgInfo, 'salesman_name');
            $remark = json_encode(["salesman_id" => $salesman_id, 'salesman_name' => $salesman_name]);
        } else {
            $org_id = '';
            $org = '';
            $remark = '获取组织信息失败';
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
        $model->created_at = time();
        if (!$model->save()) {
            echo '充值记录添加失败' . json_encode($model->errors);
        }
    }
}