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
            	switch ($model->status){
            		case 1:
            			return '正常';
            		case 2:
            			return '封禁';
            		case 3:
            			return '永久封禁';
            		default:
            			return '未知';
            	}
            }
        ];
    }
}