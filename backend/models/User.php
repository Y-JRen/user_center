<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/18
 * Time: 下午2:26
 */

namespace backend\models;


class User extends \common\models\User
{
    /**
     * 获取多条智能终端绑定信息
     *
     * @return \yii\db\ActiveQuery|CarHousekeeper
     */
    public function getCarHousekeepers()
    {
        return $this->hasMany(CarHousekeeper::className(), ['uid' => 'id']);
    }

}