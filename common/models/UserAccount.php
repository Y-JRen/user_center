<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_account".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $account
 * @property integer $type
 * @property string $bank_name
 * @property string $branch_name
 * @property integer $updated_at
 */
class UserAccount extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'account', 'type', 'updated_at'], 'required'],
            [['uid', 'type', 'updated_at'], 'integer'],
            [['account', 'bank_name', 'branch_name'], 'string', 'max' => 255],
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
            'account' => 'Account',
            'type' => 'Type',
            'bank_name' => 'Bank Name',
            'branch_name' => 'Branch Name',
            'updated_at' => 'Updated At',
        ];
    }
}
