<?php

namespace backend\models;

use Yii;

class CarManagement extends \common\models\CarManagement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'plate_number', 'frame_number', 'model_name', 'model_id', 'series_name', 'series_id', 'brand_name', 'brand_id', 'status', 'factory_id'], 'required'],
            [['uid', 'model_id', 'series_id', 'brand_id', 'status', 'factory_id', 'platform', 'created_at', 'updated_at'], 'integer'],
            [['insurance_end_date'], 'safe'],
            [['plate_number'], 'string', 'max' => 10],
            [['frame_number', 'engine_number', 'model_name', 'series_name', 'brand_name'], 'string', 'max' => 100],
            [['insurance_price'], 'string', 'max' => 30],
            [['factory_name'], 'string', 'max' => 255],
            [['driving_license'], 'string', 'max' => 1000],
            [['plate_number'], 'unique'],
        ];
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
