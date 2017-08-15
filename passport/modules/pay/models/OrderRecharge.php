<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/11
 * Time: 下午3:33
 */

namespace passport\modules\pay\models;


use common\jobs\OrderCloseJob;
use common\models\SystemConf;
use passport\helpers\Config;
use passport\models\Order;
use Yii;

class OrderRecharge extends Order
{
    public $openid;// 微信jssdk使用
    public $return_url; // 支付宝同步回调地址

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_type', 'amount', 'order_subtype'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'quick_pay'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param', 'remark'], 'string', 'max' => 255],
            ['order_id', 'unique'],
            ['order_type', 'in', 'range' => [self::TYPE_RECHARGE, self::TYPE_CONSUME, self::TYPE_REFUND, self::TYPE_CASH]],
            ['order_subtype', 'in', 'range' => array_keys(self::$rechargeSubTypeName)],
            ['order_subtype', 'validatorOrderSubType'],
            [['openid', 'return_url'], 'string'],
        ];
    }

    /**
     * 主要检测微信充值的必填参数
     */
    function validatorOrderSubType()
    {
        if ($this->order_subtype == self::SUB_TYPE_WECHAT_JSAPI && $this->isNewRecord) {
            if ($this->openid) {
                $this->remark = json_encode(['openid' => $this->openid]);
            } else {
                $this->addError('order_subtype', '参数有误');
                return false;
            }
        }
        return true;
    }

    /**
     * 初始化充值订单的属性
     */
    public function initSet()
    {
        // 新增订单时，设置平台、订单号、初始状态
        $this->quick_pay = (empty($this->quick_pay) ? 0 : $this->quick_pay);

        $this->uid = Yii::$app->user->id;
        $this->platform = Config::getPlatform();
        $this->order_id = Config::createOrderId();
        $this->status = self::STATUS_PENDING;
    }

    /**
     * 检测是否存在旧的有效订单
     *
     * 有就返回旧订单的返回信息
     * 没有返回false
     *
     * @return bool|array
     */
    public function checkOld()
    {
        /* @var $model OrderClose */
        $model = OrderClose::find()->where([
            'uid' => $this->uid,
            'status' => self::STATUS_PENDING,
            'order_type' => $this->order_type,
            'platform_order_id' => $this->platform_order_id,
            'order_subtype' => $this->order_subtype
        ])->one();

        if ($model && $model->amount == $this->amount) {
            // 充值子类型、金额完成一样，直接返回
            $data = $this->getCache($model->id);
            if (!empty($data)) {
                return $data;
            }
        }

        // 关闭$model的订单，新创建一个
        $model->close();

        return false;
    }


    /**
     * 创建充值订单后，添加定时关闭
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if (in_array($this->order_subtype, OrderClose::$allowCloseSubtype)) {
                Yii::$app->queue_second->delay(SystemConf::getValue('recharge_order_valid_time') * 60)->push(new OrderCloseJob([
                    'order_id' => $this->order_id
                ]));
            }
        }
    }

    /**
     * 将充值订单的返回信息放入缓存中
     * @param $id
     * @param $data
     */
    public function addCache($id, $data)
    {
        /* @var $redis yii\redis\Connection */
        $redis = Yii::$app->redis;
        $redis->set("order_{$id}_return_data", json_encode($data));
        $redis->expire("order_{$id}_return_data", SystemConf::getValue('recharge_order_valid_time') * 60);
    }

    /**
     * 获取订单的返回信息
     * @param $id
     * @return mixed
     */
    public function getCache($id)
    {
        /* @var $redis yii\redis\Connection */
        $redis = Yii::$app->redis;
        $data = $redis->get("order_{$id}_return_data");
        return json_decode($data, true);
    }

}