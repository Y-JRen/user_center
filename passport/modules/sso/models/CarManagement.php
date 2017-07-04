<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/4
 * Time: 10:18
 */

namespace passport\modules\sso\models;


class CarManagement extends \common\models\CarManagement
{
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    public function scenarios()
    {
        $data = $this->attributes;
        unset($data['id'], $data['uid'], $data['status']);
        return [
            self::SCENARIO_DEFAULT => [],
            self::SCENARIO_UPDATE => $data,
            self::SCENARIO_DELETE => ['status', 'updated_at'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    /**
     * 返回字段设置
     * @return array
     */
    public function fields()
    {
        return parent::fields();
    }

    public function beforeSave($insert)
    {

        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->plate_number = strtoupper($this->plate_number);

        return true;
    }
}