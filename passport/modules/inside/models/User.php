<?php

namespace passport\modules\inside\models;

use common\models\UserInfo;
use function foo\func;
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
class User extends \common\models\User
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
        // 删除密码
        unset($data['passwd']);
        // 格式化客户端类型
        $data['client_type'] = function ($model) {
            $platform = ArrayHelper::getValue(Config::$platformArray, $model->from_platform);
            if (empty($model->client_type)) {
                return $platform;
            } else {
                return $platform . '--' . strtoupper($model->client_type);
            }
        };

        // 获取用户扩展信息【金额、实名认证】
        $controllerAction = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        if (in_array($controllerAction, ['user/info'])) {
            $data['user_balance'] = function ($model) {
                return (float)ArrayHelper::getValue($model->balance, 'amount', 0);
            };

            $data['user_freeze'] = function ($model) {
                return (float)ArrayHelper::getValue($model->freeze, 'amount', 0);
            };

            $data['is_real'] = function ($model) {
                return ($model->userInfo && $model->userInfo->verifyReal()) ? '已认证' : '未认证';
            };

            $data['real_name'] = function ($model) {
                return ($model->userInfo && $model->userInfo->verifyReal()) ? $model->userInfo->real_name : '';
            };
            
            $data['card_number'] = function ($model) {
                return ($model->userInfo && $model->userInfo->verifyReal()) ? $model->userInfo->card_number : '';
            };
        }

        return $data;
    }
}
