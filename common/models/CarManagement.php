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
 * @property string $car_brand_son_type_name
 * @property integer $car_brand_son_type_id
 * @property string $car_brand_type_name
 * @property integer $car_brand_type_id
 * @property string $brand_name
 * @property integer $brand_id
 * @property string $insurance_end_date
 * @property string $insurance_price
 * @property integer $status
 * @property integer $platform
 * @property integer $created_at
 * @property integer $updated_at
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
    public function rules()
    {
        return [
            [['uid', 'plate_number', 'frame_number', 'engine_number', 'car_brand_son_type_name', 'car_brand_son_type_id', 'car_brand_type_name', 'car_brand_type_id', 'brand_name', 'brand_id', 'insurance_end_date', 'insurance_price', 'status'], 'required'],
            [['uid', 'car_brand_son_type_id', 'car_brand_type_id', 'brand_id', 'status', 'platform', 'created_at', 'updated_at'], 'integer'],
            [['insurance_end_date'], 'safe'],
            [['plate_number'], 'string', 'max' => 10],
            [['frame_number', 'engine_number', 'car_brand_son_type_name', 'car_brand_type_name', 'brand_name'], 'string', 'max' => 100],
            [['insurance_price'], 'string', 'max' => 30],
            [['plate_number'], 'unique'],
        ];
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
            'car_brand_son_type_name' => '车型名称',
            'car_brand_son_type_id' => '车型ID',
            'car_brand_type_name' => '车系名称',
            'car_brand_type_id' => '车系ID',
            'brand_name' => '品牌名称',
            'brand_id' => '品牌ID',
            'insurance_end_date' => '保险到期时间',
            'insurance_price' => '保险价格区间',
            'status' => '状态',
            'platform' => '车辆来源',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
