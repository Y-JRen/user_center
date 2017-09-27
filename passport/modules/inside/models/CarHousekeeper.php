<?php

namespace passport\modules\inside\models;

use Yii;


class CarHousekeeper extends \common\models\CarHousekeeper
{

    public function fields()
    {
        return [
            'terminal_no',
            'created_at',
            'car'
        ];
    }

    public function getCar()
    {
        return $this->hasOne(CarManagement::className(), ['id' => 'car_management_id']);
    }
}
