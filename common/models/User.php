<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $phone
 * @property string $user_name
 * @property string $email
 * @property string $passwd
 * @property integer $status
 * @property integer $from_platform
 * @property string $from_channel
 * @property integer $reg_time
 * @property string $reg_ip
 * @property integer $login_time
 */
class User extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'email', 'passwd', 'from_channel', 'reg_time', 'reg_ip', 'login_time'], 'required'],
            [['status', 'from_platform', 'reg_time', 'login_time'], 'integer'],
            [['phone'], 'string', 'max' => 12],
            [['user_name'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 20],
            [['passwd'], 'string', 'max' => 50],
            [['from_channel'], 'string', 'max' => 128],
            [['reg_ip'], 'string', 'max' => 15],
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
            'user_name' => 'User Name',
            'email' => 'Email',
            'passwd' => 'Passwd',
            'status' => 'Status',
            'from_platform' => 'From Platform',
            'from_channel' => 'From Channel',
            'reg_time' => 'Reg Time',
            'reg_ip' => 'Reg Ip',
            'login_time' => 'Login Time',
        ];
    }
}
