<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_info".
 *
 * @property integer $uid
 * @property string $real_name
 * @property string $card_number
 * @property integer $birthday
 * @property integer $sex
 * @property integer $is_real
 * @property string $area
 * @property string $city
 * @property string $county
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserInfo extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'required'],
            [['uid', 'birthday', 'sex', 'is_real', 'created_at', 'updated_at'], 'integer'],
            [['real_name'], 'string', 'max' => 20],
            [['card_number'], 'string', 'max' => 30],
            [['area', 'city', 'county'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'real_name' => 'Real Name',
            'card_number' => 'Card Number',
            'birthday' => 'Birthday',
            'sex' => 'Sex',
            'is_real' => 'Is Real',
            'area' => 'Area',
            'city' => 'City',
            'county' => 'County',
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
