<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/8/25
 * Time: 上午9:45
 */

namespace common\traits;

use common\models\FreezeRecord;
use common\models\Order;
use common\models\PreOrder;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 非消费、提现订单冻结操作相关(备用金)
 *
 * Trait ConsumeTrait
 * @package common\traits
 */
trait FreezeTrait
{
    /**
     * 添加手续费
     * @param Order|PreOrder $order 充值订单
     * @throws Exception
     */
    public function createFreeze($order)
    {
        $model = new FreezeRecord();
        $model->uid = $order->uid;
        $model->order_no = $order->order_id;
        $model->use = ArrayHelper::getValue($order->rechargeExtend, 'use');
        $model->amount = $order->amount;
        $model->status = FreezeRecord::STATUS_FREEZE_OK;
        if ($model->save()) {
            $this->freeze($model);
        } else {
            Yii::error(var_export($model->errors, true), 'FreezeTrait');
            throw new Exception('创建冻结记录失败');
        }
    }

    /**
     * 解冻
     * @param $order_no
     * @throws Exception
     */
    public function thaw($order_no, $uid, $amount)
    {
        /* @var $model FreezeRecord */
        $model = FreezeRecord::find()->where(['order_no' => $order_no])->one();
        if (!$model) {
            throw new Exception('不存在该笔冻结');
        }

        if (($model->uid != $uid) || ($model->amount != $amount)) {
            throw new Exception('解冻信息不匹配');
        }

        if ($model->status == FreezeRecord::STATUS_FREEZE_OK) {
            $model->status = FreezeRecord::STATUS_THAW_OK;
            if ($model->save()) {
                $this->unfreeze($model);
            }
        } else {
            throw new Exception('该笔冻结已解冻');
        }
    }

    /**
     * 冻结步骤
     * @param FreezeRecord $record 冻结记录
     * @throws Exception
     */
    protected function freeze($record)
    {
        if (!$record->user) {
            throw new Exception('当前记录用户不存在');
        }
        if (!$record->user->userBalance->cutMoney($record->amount, $record->order)) {
            throw new Exception('余额扣除失败');
        }

        if (!$record->user->userFreeze->addMoney($record->amount, $record->order)) {
            throw new Exception('冻结金额添加失败');
        }
    }

    /**
     * 解冻步骤
     * @param FreezeRecord $record 冻结记录
     * @throws Exception
     */
    protected function unfreeze($record)
    {
        if (!$record->user) {
            throw new Exception('当前记录用户不存在');
        }

        if (!$record->user->userFreeze->cutMoney($record->amount, $record->order)) {
            throw new Exception('冻结金额扣除失败');
        }

        if (!$record->user->userBalance->addMoney($record->amount, $record->order)) {
            throw new Exception('余额增加失败');
        }
    }

}