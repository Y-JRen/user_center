<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/4
 * Time: 上午11:20
 */

namespace passport\models;

use common\jobs\PreOrderCloseJob;
use common\models\RechargeExtend;
use common\models\SystemConf;
use Yii;
use yii\helpers\ArrayHelper;

class PreOrder extends \common\models\PreOrder
{
    public $use;// 用途

    public function rules()
    {
        $rules = [
            ['use', 'string'],
        ];

        return ArrayHelper::merge(
            parent::rules(),
            $rules
        );
    }

    /**
     * @return array|bool|mixed
     */
    public function checkOld()
    {
        /* @var $model self */
        $model = self::find()->where([
            'uid' => $this->uid,
            'status' => Order::STATUS_PENDING,
            'platform_order_id' => $this->platform_order_id,
        ])->one();


        if ($model) {
            if ((float)$model->amount == (float)$this->amount) {
                // 金额一样,缓存数据存在，直接返回
                $data = $this->getCache($model->id);
                if (!empty($data)) {
                    return $data;
                }
            }

            $model->close();
        }

        return false;
    }

    /**
     * 创建预处理订单后，添加定时关闭
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
                $model->object_name = $this::className();
                $model->object_id = $this->id;
                $model->order_no = $this->order_id;
                $model->use = $this->use;
                if (!$model->save()) {
                    Yii::error(var_export($model->errors, true));
                }
            }

            Yii::$app->queue_second->delay(SystemConf::getValue('pre_order_valid_time') * 86400)->push(new PreOrderCloseJob([
                'id' => $this->id
            ]));
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * 将预处理订单的返回信息放入缓存中
     * @param $id
     * @param $data
     */
    public function addCache($id, $data)
    {
        /* @var $redis yii\redis\Connection */
        $redis = Yii::$app->redis;
        $redis->set("pre_order_{$id}_return_data", json_encode($data));
        $redis->expire("pre_order_{$id}_return_data", SystemConf::getValue('pre_order_valid_time') * 86400);
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
        $data = $redis->get("pre_order_{$id}_return_data");
        return json_decode($data, true);
    }
}