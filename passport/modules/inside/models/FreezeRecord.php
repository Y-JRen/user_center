<?php

namespace passport\modules\inside\models;

use yii\helpers\ArrayHelper;

class FreezeRecord extends \common\models\FreezeRecord
{
    public function scenarios()
    {
        return [
            'view' => ['order_no', 'uid', 'amount', 'use'],
            'default' => ['phone', 'order_no', 'uid', 'amount', 'use', 'status','created_at']
        ];
    }

    public function fields()
    {
        return ArrayHelper::getValue($this->scenarios(), $this->scenario);
    }

    /**
     * @return \yii\db\ActiveQuery|User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return ArrayHelper::getValue($this->user, 'phone');
    }
}
