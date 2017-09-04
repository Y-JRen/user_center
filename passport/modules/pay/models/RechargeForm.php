<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/4
 * Time: 上午11:47
 */

namespace passport\modules\pay\models;


use common\helpers\ConfigHelper;
use passport\models\Order;
use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $uid
 * @property string $platform_order_id
 * @property string $order_id
 * @property integer $order_type
 * @property string $order_subtype
 * @property string $amount
 * @property string $receipt_amount
 * @property string $counter_fee
 * @property string $discount_amount
 * @property integer $status
 * @property string $desc
 * @property integer $notice_status
 * @property string $notice_platform_param
 * @property string $remark
 * @property integer $platform
 * @property integer $quick_pay
 *
 */
class RechargeForm extends \yii\base\Model
{
    public $uid;// 用户id
    public $platform_order_id = '';// 平台订单号
    public $order_id;// 会员中心单号
    public $order_type;// 类型
    public $order_subtype;// 子类型
    public $amount;// 金额
    public $receipt_amount;// 实际金额
    public $counter_fee;// 手续费
    public $discount_amount;// 优惠金额
    public $status;// 状态
    public $desc;// 简述
    public $notice_status;// 通知状态
    public $notice_platform_param = '';// 回调通知参数
    public $remark = '';// 备注
    public $platform;// 平台
    public $quick_pay = 0;// 是否快捷支付
    public $openid;// 微信 js sdk 使用
    public $return_url; // 支付宝同步回调地址


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_type', 'status', 'notice_status', 'quick_pay'], 'integer'],
            [['amount', 'receipt_amount', 'counter_fee', 'discount_amount'], 'number'],
            [['order_subtype', 'notice_platform_param', 'remark', 'desc', 'openid', 'return_url'], 'string'],
        ];
    }

    /**
     * 初始化充值订单的属性
     */
    public function initSet()
    {
        // 新增订单时，设置平台、订单号、初始状态
        $this->quick_pay = (empty($this->quick_pay) ? 0 : $this->quick_pay);

        $this->uid = Yii::$app->user->id;
        $this->platform = ConfigHelper::getPlatform();
        $this->order_id = ConfigHelper::createOrderId();
        $this->status = Order::STATUS_PENDING;

        $this->receipt_amount = (float)$this->receipt_amount;
        $this->counter_fee = (float)$this->counter_fee;
        $this->discount_amount = (float)$this->discount_amount;
    }

    /**
     * 创建充值对象
     */
    public function createObject()
    {
        $this->initSet();

        // 电商平台拉卡拉类型走预处理流程
        if (ConfigHelper::getPlatform() == 1 && $this->order_subtype == Order::SUB_TYPE_LAKALA) {
            // 预处理订单流程
            $model = new PreOrder();
        } else {
            // 订单流程
            $model = new OrderRecharge();
        }

        $model->attributes = $this->attributes;

        return $model;
    }
}