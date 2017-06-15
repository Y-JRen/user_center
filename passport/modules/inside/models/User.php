<?php

namespace passport\modules\inside\models;

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
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            ['phone', 'required'],
            ['phone', 'match', 'pattern' => '/^1\d{10}/', 'message' => '手机号格式不正确'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => '手机号'
        ];
    }
}
