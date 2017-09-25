<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "car_management".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $plate_number
 * @property string $frame_number
 * @property string $engine_number
 * @property string $model_name
 * @property integer $model_id
 * @property string $series_name
 * @property integer $series_id
 * @property string $brand_name
 * @property integer $brand_id
 * @property string $insurance_end_date
 * @property string $insurance_price
 * @property integer $status
 * @property string $factory_name
 * @property integer $factory_id
 * @property integer $platform
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $driving_license
 */
class CarManagement extends BaseModel
{
    const STATUS_DELETE = 10;
    const STATUS_SHOW = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'car_management';
    }




    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'plate_number' => '车牌号',
            'frame_number' => '车架号',
            'engine_number' => '发动机编号',
            'model_name' => '车型名称',
            'model_id' => '车型',
            'series_name' => '车系名称',
            'series_id' => '车系',
            'brand_name' => '品牌名称',
            'brand_id' => '品牌',
            'factory_name' => '厂商名称',
            'factory_id' => '厂商',
            'insurance_end_date' => '保险到期时间',
            'insurance_price' => '保险价格区间',
            'status' => '状态',
            'platform' => '车辆来源',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'driving_license' => '凭证',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
