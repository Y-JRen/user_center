<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/15
 * Time: 17:28
 */

namespace passport\modules\inside\models;


use common\models\UserInfo;
use passport\helpers\Config;
use Yii;
use yii\helpers\ArrayHelper;

class Order extends \passport\models\Order
{
    public $openid;// 微信jssdk使用
    public $return_url; // 支付宝同步回调地址
    public $use;// 用途

    public function rules()
    {
        return [
            [['uid', 'order_type', 'amount', 'desc'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'platform', 'quick_pay'], 'integer'],
            [['amount', 'counter_fee', 'discount_amount', 'receipt_amount'], 'number'],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param'], 'string', 'max' => 255],
            ['order_id', 'unique'],
            [['openid', 'return_url', 'remark', 'use'], 'string'],
            ['platform_order_id', 'required', 'when' => function ($model) {
                return $model->order_type != self::TYPE_RECHARGE;
            }],
        ];
    }

    public function initSet()
    {
        // 新增订单时，设置平台、订单号、初始状态
        $this->quick_pay = empty($this->quick_pay) ? 0 : $this->quick_pay;
        $this->platform = Config::getPlatform();
        $this->order_id = Config::createOrderId();
        $this->status = self::STATUS_PENDING;
    }

    /**
     * 创建贷款入账的消费订单
     * @return bool|Order
     */
    public function createLoanConsumeOrder()
    {
        $model = new self();
        $model->uid = $this->uid;
        $model->platform_order_id = $this->platform_order_id;
        $model->amount = $this->amount;
        $model->order_type = self::TYPE_CONSUME;
        $model->order_subtype = self::SUB_TYPE_LOAN_RECORD;
        $model->desc = "订单号[{$model->platform_order_id}]贷款进入冻结金额";
        if ($model->save()) {
            return $model;
        } else {
            return false;
        }
    }

    /**
     * 添加消费订单
     * @param array $data
     * @return bool|Order
     */
    public function createConsumeOrder($data = [])
    {
        $model = new self();
        $model->uid = $this->uid;
        $model->platform_order_id = $this->platform_order_id;
        $model->amount = $this->amount;
        $model->order_type = self::TYPE_CONSUME;
        $model->notice_status = 4;
        $model->load($data, '');
        if ($model->save()) {
            return $model;
        } else {
            return false;
        }
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['uid' => 'uid']);
    }

    public function fields()
    {
        $controllerAction = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

        if (in_array($controllerAction, ['trade/info', 'trade/search'])) {// 针对pos机的请求返回
            return [
                'order_id',
                'platform_order_id',
                'phone' => function ($model) {
                    return ArrayHelper::getValue($model->user, 'phone');
                },
                'real_name' => function ($model) {
                    if ($userInfo = $model->userInfo) {
                        return $userInfo->verifyReal() ? $userInfo->real_name : '';
                    } else {
                        return '';
                    }
                },
                'desc',
                'type',
                'amount',
                'receipt_amount',
                'counter_fee',
                'discount_amount',
                'status',
                'statusName' => function ($model) {
                    return $this->orderStatus;
                },
                'quick_pay',
                'order_type',
                'created_at'
            ];
        }
        return parent::fields();
    }


}