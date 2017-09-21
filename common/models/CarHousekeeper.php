<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "car_housekeeper".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $terminal_no
 * @property integer $car_management_id
 * @property string $client_device_no
 * @property integer $created_at
 * @property integer $updated_at
 */
class CarHousekeeper extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'car_housekeeper';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'terminal_no', 'car_management_id'], 'required'],
            [['uid', 'car_management_id', 'created_at', 'updated_at'], 'integer'],
            [['terminal_no', 'client_device_no'], 'string', 'max' => 32],
            [['terminal_no'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'terminal_no' => '智能终端设备号',
            'car_management_id' => 'Car Management ID',
            'client_device_no' => 'Client Device No',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
