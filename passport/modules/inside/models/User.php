<?php

namespace passport\modules\inside\models;

use passport\helpers\Config;
use Yii;
use yii\helpers\ArrayHelper;

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
 * @property string $client_type
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
            ['user_name', 'string'],
            ['phone', 'match', 'pattern' => '/^1\d{10}/', 'message' => '手机号格式不正确'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => '手机号'
        ];
    }

    /**
     * 格式化client_type
     *
     * @return array
     */
    public function fields()
    {
        $data = parent::fields();
        unset($data['passwd']);
        $data['client_type'] = function ($model) {
            $platform = ArrayHelper::getValue(Config::$platformArray, $model->from_platform);
            if (empty($model->client_type)) {
                return $platform;
            } else {
                return $platform . '--' . strtoupper($model->client_type);
            }
        };
        return $data;
    }
}
