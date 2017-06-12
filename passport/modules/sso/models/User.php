<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/8
 * Time: 16:23
 */

namespace passport\modules\sso\models;


use common\models\UserBalance;
use common\models\UserFreeze;
use yii\helpers\ArrayHelper;
use common\models\UserInfo;

class User extends \common\models\User
{
    /**
     * 获取用户余额
     * @return \yii\db\ActiveQuery
     */
    public function getBalance()
    {
        return $this->hasOne(UserBalance::className(), ['uid' => 'id']);
    }

    /**
     * 获取用户冻结余额
     * @return \yii\db\ActiveQuery
     */
    public function getFreeze()
    {
        return $this->hasOne(UserFreeze::className(), ['uid' => 'id']);
    }
    
    /**
     * 获取用户实名信息
     * @return \yii\db\ActiveQuery
     */
    public function getInfo()
    {
    	return $this->hasOne(UserInfo::className(), ['uid' => 'id']);
    }

    public function fields()
    {
        return [
            'uid' => function ($model) {
                return $model->id;
            },
            'user_name' => function ($model) {
                return $model->phone;
            },
            'phone',
            'balance' => function ($model) {
                return ArrayHelper::getValue($model->balance, 'amount', 0);
            },
            'freeze' => function ($model) {
                return ArrayHelper::getValue($model->freeze, 'amount', 0);
            },
            'status' => function ($model){
            	return $model->status;
            },
            'is_auth'=>function($model){
            	return ArrayHelper::getValue($model->info, 'uid', 0) ? 1 : 0;
            }
        ];
    }
}