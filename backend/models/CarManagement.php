<?php

namespace backend\models;

use Yii;

class CarManagement extends \common\models\CarManagement
{
    public $file;
    public $delete_driving_license;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'plate_number', 'frame_number', 'series_name', 'series_id', 'brand_name', 'brand_id', 'status', 'factory_id'], 'required'],
            [['uid', 'model_id', 'series_id', 'brand_id', 'status', 'factory_id', 'platform', 'created_at', 'updated_at'], 'integer'],
            [['insurance_end_date'], 'safe'],
            [['plate_number'], 'string', 'max' => 10],
            [['frame_number', 'engine_number', 'model_name', 'series_name', 'brand_name'], 'string', 'max' => 100],
            [['insurance_price'], 'string', 'max' => 30],
            [['factory_name'], 'string', 'max' => 255],
            [['driving_license', 'delete_driving_license'], 'string', 'max' => 1000],
            [['plate_number'], 'unique'],
        ];
    }

    public function beforeSave($insert)
    {

        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (empty($this->model_id)) {
            $this->model_name = '';
        }

        if (!empty($this->delete_driving_license) && !empty($this->driving_license)) {
            $data = explode(',', $this->driving_license);
            $deleteData = explode(',', $this->delete_driving_license);
            $license = array_diff($data, $deleteData);
            $this->driving_license = join(',', $license);
        }

        $this->plate_number = strtoupper($this->plate_number);
        return true;
    }
}
