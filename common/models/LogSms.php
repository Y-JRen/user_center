<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log_sms".
 *
 * @property integer $id
 * @property string $phone
 * @property integer $platform
 * @property string $info
 * @property integer $status
 * @property integer $created_at
 */
class LogSms extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_sms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'platform', 'info', 'status', 'created_at','resmsg'], 'required'],
            [['id', 'platform', 'status', 'created_at'], 'integer'],
            [['phone'], 'string', 'max' => 12],
            [['info'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'platform' => 'Platform',
            'info' => 'Info',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
