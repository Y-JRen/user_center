<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/13
 * Time: 9:51
 */

namespace passport\models;

use common\jobs\OrderCloseJob;
use common\models\RechargeExtend;
use common\models\SystemConf;
use passport\helpers\Config;
use passport\modules\pay\models\OrderClose;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Order
 * @package passport\models
 */
class Order extends \common\models\Order
{
    public function fields()
    {
        return [
            'platform_order_id',
            'order_id',
            'order_type',
            'order_subtype',
            'amount',
            'receipt_amount',
            'counter_fee',
            'discount_amount',
            'desc',
            'status',
            'statusName' => function ($model) {
                return $model->orderStatus;
            },
            'notice_platform_param',
            'platform' => function ($model) {
                return ArrayHelper::getValue(Config::getPlatformArray(), $model->platform);
            },
            'created_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at);
            },
            'updated_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at);
            }
        ];
    }


    // 交易记录默认搜索的状态
    public static $defaultSearchStatus = [
        self::STATUS_PROCESSING,
        self::STATUS_SUCCESSFUL,
        self::STATUS_FAILED,
        self::STATUS_PENDING,
        self::STATUS_TRANSFER,
    ];

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
}