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
    public static $sexArr = [
        1 => '男',
        2 => '女',
        3 => '保密'
    ];

    public static $realArr = [
        0 => '没有',
        1 => '已认证',
        2 => '认证失败',
        3 => '待审核'
    ];

    // 资金状态数组
    public static $fundsArr = [
        1 => '正常',
        2 => '异常',
    ];

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
            'real_name' => '真实姓名',
            'card_number' => '身份证号码',
            'birthday' => '生日',
            'sex' => '性别',
            'is_real' => '是否实名认证',
            'area' => '省',
            'city' => '市',
            'county' => '区',
            'funds_status' => '资金状态',
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

    /**
     * 获取用户扩展信息
     *
     * @param $uid
     * @return array|UserInfo|null|\yii\db\ActiveRecord
     */
    public static function getInfo($uid)
    {
        $model = self::find()->where(['uid' => $uid])->one();
        if (empty($model)) {
            $model = new UserInfo(['uid' => $uid]);
        }
        return $model;
    }

    /**
     * 判断用户是否实名认证
     */
    public function verifyReal()
    {
        return ($this->is_real == 1);
    }
}
