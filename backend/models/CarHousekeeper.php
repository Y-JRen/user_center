<?php

namespace backend\models;

use Yii;

/**
 * @property CarManagement $carManagement
 */
class CarHousekeeper extends \common\models\CarHousekeeper
{
    /**
     * 获取车辆信息
     * @return \yii\db\ActiveQuery|CarManagement
     */
    public function getCarManagement()
    {
        return $this->hasOne(CarManagement::className(), ['id' => 'car_management_id']);
    }

}
