<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "car_housekeeper".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $terminal_no
 * @property integer $car_management_id
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
            [['uid', 'terminal_no', 'car_management_id', 'created_at', 'updated_at'], 'required'],
            [['uid', 'car_management_id', 'created_at', 'updated_at'], 'integer'],
            [['terminal_no'], 'string', 'max' => 32],
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
            'terminal_no' => '终端序列号',
            'car_management_id' => 'Car Management ID',
            'created_at' => '创建时间',
            'updated_at' => '最后更新时间',
        ];
    }
}
