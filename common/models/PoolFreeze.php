<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "pool_freeze".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $order_id
 * @property string $amount
 * @property string $desc
 * @property string $before_amount
 * @property string $after_amount
 * @property string $remark
 * @property integer $created_at
 */
class PoolFreeze extends BaseModel
{
    const STYLE_PLUS = 'plus';
    const STYLE_LESS = 'less';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pool_freeze';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_id', 'amount', 'desc', 'before_amount', 'after_amount', 'created_at'], 'required'],
            [['uid', 'created_at'], 'integer'],
            [['amount', 'before_amount', 'after_amount'], 'number'],
            [['order_id', 'desc'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'order_id' => 'Order ID',
            'amount' => 'Amount',
            'desc' => 'Desc',
            'before_amount' => 'Before Amount',
            'after_amount' => 'After Amount',
            'remark' => 'Remark',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param $uid
     * @return mixed
     */
    public static function getUserBalance($uid)
    {
        $result = self::find()->select('after_amount')->where(['uid' => $uid])->orderBy('id DESC')->asArray()->one();
        return ArrayHelper::getValue($result, 'after_amount', 0);
    }
}
