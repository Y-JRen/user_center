<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/5
 * Time: 13:49
 */

namespace backend\models;


use Yii;
use yii\behaviors\TimestampBehavior;

class Coupon extends \common\models\Coupon
{
    public $start_time_fix;
    public $end_time_fix;

    public $start_time_immediate;
    public $end_time_immediate;

    public static $startTimeArray = [
        0 => '立即生效',
        1 => '1天后生效',
        2 => '2天后生效',
        3 => '3天后生效',
        4 => '4天后生效',
        5 => '5天后生效',
        6 => '6天后生效',
        7 => '7天后生效',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['platform_id', 'dealer_id', 'name', 'short_name', 'type', 'number', 'amount', 'effective_way', 'upper_limit', 'superposition', 'tips', 'status', 'receive_start_time', 'receive_end_time'], 'required'],
            [['platform_id', 'dealer_id', 'type', 'number', 'effective_way', 'upper_limit', 'superposition', 'status', 'created_at', 'updated_at'], 'integer'],
            [['start_time_fix', 'end_time_fix', 'start_time_immediate', 'end_time_immediate', 'start_time', 'end_time'], 'safe'],
            [['amount'], 'number'],
            [['name', 'short_name', 'image', 'desc'], 'string', 'max' => 255],
            [['tips'], 'string', 'max' => 30],
            ['effective_way', 'effectiveWayValidator'],
            ['receive_end_time', 'receiveEndTimeValidator'],
        ];
    }

    /**
     * 生效时间校验
     * @return bool
     */
    public function effectiveWayValidator()
    {
        if ($this->effective_way == self::EFFECTIVE_WAY_FIXED) {
            if (strtotime($this->end_time_fix) <= strtotime($this->start_time_fix)) {
                $this->addError('end_time_fix', '固定时间生效时，生效结束时间必须大于生效开始时间');
                return false;
            }
        } else {
            if ($this->end_time_immediate < 1) {
                $this->addError('end_time_immediate', ' 领取立即生效时，生效时长必须大于1天');
                return false;
            }
        }
        return true;
    }

    /**
     * 领取时间校验
     * @param $attribute
     * @return bool
     */
    public function receiveEndTimeValidator($attribute)
    {
        if (strtotime($this->receive_end_time) <= strtotime($this->receive_start_time)) {
            $this->addError($attribute, '领取截止时间必须大于领取开始时间');
            return false;
        }
        return true;
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'platform_id' => '平台',
            'dealer_id' => '经销商',
            'name' => '名称',
            'short_name' => '短名称',
            'image' => '图片',
            'type' => '类型',
            'number' => '库存数量',
            'amount' => '价格面额',
            'effective_way' => '有效方式',
            'start_time' => '生效起始时间',
            'end_time' => '生效结束时间',
            'start_time_fix' => '生效起始时间',
            'end_time_fix' => '生效结束时间',
            'start_time_immediate' => '领取后',
            'end_time_immediate' => '生效时长',
            'upper_limit' => '用户领取上限',
            'superposition' => '是否叠加使用',
            'tips' => '使用提示',
            'desc' => '其他说明',
            'status' => '状态',
            'receive_start_time' => '领取开始时间',
            'receive_end_time' => '领取截止时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function beforeSave($insert)
    {

        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->effective_way == self::EFFECTIVE_WAY_FIXED) {
            $this->start_time = strtotime($this->start_time_fix);
            $this->end_time = strtotime($this->end_time_fix);
        } else {
            $this->start_time = $this->start_time_immediate;
            $this->end_time = $this->end_time_immediate;
        }

        $this->receive_start_time = strtotime($this->receive_start_time);
        $this->receive_end_time = strtotime($this->receive_end_time);

        return true;
    }

    public function afterFind()
    {
        if ($this->effective_way == self::EFFECTIVE_WAY_FIXED) {
            $this->start_time_fix = Yii::$app->formatter->asDatetime($this->start_time);
            $this->end_time_fix = Yii::$app->formatter->asDatetime($this->end_time);
        } else {
            $this->start_time_immediate = $this->start_time;
            $this->end_time_immediate = $this->end_time;
        }

        $this->receive_start_time = Yii::$app->formatter->asDatetime($this->receive_start_time);
        $this->receive_end_time = Yii::$app->formatter->asDatetime($this->receive_end_time);
    }

    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }
}