<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_balance".
 *
 * @property integer $uid
 * @property string $amount
 * @property integer $updated_at
 */
class UserBalance extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_balance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'amount', 'updated_at'], 'required'],
            [['uid', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['amount'], 'validatorAmount']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'amount' => 'Amount',
            'updated_at' => 'Updated At',
        ];
    }

    public function validatorAmount()
    {
        if ($this->amount < 0) {
            $this->addError('amount', '用户可用余额值必须大于等于0');
            return false;
        }
        return true;
    }

    /**
     * 增加用户余额
     * @param $amount
     * @return bool
     */
    public function plus($amount)
    {
        $this->amount += $amount;
        $this->updated_at = time();
        return $this->save();
    }

    /**
     * 减少用户余额
     * @param $amount
     * @return bool
     */
    public function less($amount)
    {
        $this->amount -= $amount;
        $this->updated_at = time();
        return $this->save();
    }

    /**
     * 增加用户余额
     * 为了替换 plus 方法
     *
     * @param $amount
     * @param Order $order
     * @return bool
     */
    public function addMoney($amount, $order)
    {
        $this->amount += $amount;
        $this->updated_at = time();
        if ($this->save()) {
            return $order->addPoolBalance(PoolBalance::STYLE_PLUS);
        } else {
            return false;
        }
    }

    /**
     * 减少用户余额
     * 为了替换 less 方法
     *
     * @param $amount
     * @param Order $order
     * @return bool
     */
    public function cutMoney($amount, $order)
    {
        $this->amount -= $amount;
        $this->updated_at = time();
        if ($this->save()) {
            return $order->addPoolBalance(PoolBalance::STYLE_LESS);
        } else {
            return false;
        }
    }
}
