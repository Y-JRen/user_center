<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_freeze".
 *
 * @property integer $uid
 * @property double $amount
 * @property integer $updated_at
 */
class UserFreeze extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_freeze';
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
}
