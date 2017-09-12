<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/11
 * Time: 下午3:33
 */

namespace passport\modules\pay\models;


use common\jobs\OrderCloseJob;
use common\models\RechargeExtend;
use common\models\SystemConf;
use passport\models\Order;
use Yii;

class OrderRecharge extends Order
{
    public $openid;// 微信jssdk使用
    public $return_url; // 支付宝同步回调地址
    public $use;// 用途

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_type', 'amount', 'order_subtype'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at', 'quick_pay'], 'integer'],
            [['amount', 'receipt_amount', 'counter_fee', 'discount_amount'], 'number'],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param'], 'string', 'max' => 255],
            ['order_id', 'unique'],
            ['order_subtype', 'in', 'range' => array_keys(self::$rechargeSubTypeName)],
            ['order_subtype', 'validatorOrderSubType'],
            [['openid', 'return_url', 'remark', 'use'], 'string'],
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
     * 检测是否存在旧的有效订单,去除线下充值，线下充值有临界状态
     *
     * 同一电商订单号，只会存在一个待处理的订单
     *
     * 相关条件
     * 1、充值订单
     * 2、待处理
     * 3、同一用户
     * 4、子类型相同，充值金额也相同，直接返回上次的相关充值信息
     *
     *
     *
     * 有就返回旧订单的返回信息
     * 没有返回false
     *
     * @return bool|array
     */
    public function checkOld()
    {
        /* @var $models OrderClose[] */
        $models = OrderClose::find()->where([
            'uid' => $this->uid,
            'status' => self::STATUS_PENDING,
            'order_type' => $this->order_type,
            'platform_order_id' => $this->platform_order_id,
        ])->andWhere("order_subtype != :order_subtype", [':order_subtype' => Order::SUB_TYPE_LINE_DOWN])->all();

        $data = [];
        foreach ($models as $model) {
            if (!empty($data)) {
                $model->close();
                continue;
            }

            if (($model->order_subtype == $this->order_subtype) && ($model->amount == $this->amount)) {
                // 充值子类型、金额完成一样，直接返回
                $data = $this->getCache($model->id);
                if (!empty($data)) {
                    continue;
                }
            }

            // 关闭$model的订单，新创建一个
            $model->close();
        }

        if ($data) {
            return $data;
        } else {
            return false;
        }
    }


    /**
     * 创建充值订单后
     *
     * 1、添加扩展记录
     * 2、添加定时关闭
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // 扩展表添加记录
            if (!empty($this->use)) {
                $model = new RechargeExtend();
                $model->uid = $this->uid;
                $model->order_id = $this->id;
                $model->order_no = $this->order_id;
                $model->use = $this->use;
                if (!$model->save()) {
                    Yii::error(var_export($model->errors, true));
                }
            }

            // 添加定时任务
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