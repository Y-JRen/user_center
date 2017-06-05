<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log_login".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $platform
 * @property string $platform_type
 * @property string $ip
 * @property integer $created_at
 */
class LogLogin extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'platform', 'platform_type', 'ip', 'created_at'], 'required'],
            [['uid', 'platform', 'created_at'], 'integer'],
            [['platform_type'], 'string', 'max' => 20],
            [['ip'], 'string', 'max' => 15],
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
            'platform' => 'Platform',
            'platform_type' => 'Platform Type',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }
}
