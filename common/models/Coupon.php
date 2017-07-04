<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "coupon".
 *
 * @property integer $id
 * @property integer $platform_id
 * @property integer $dealer_id
 * @property string $name
 * @property string $short_name
 * @property string $image
 * @property integer $type
 * @property integer $number
 * @property string $amount
 * @property integer $effective_way
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $upper_limit
 * @property integer $superposition
 * @property string $tips
 * @property string $desc
 * @property integer $status
 * @property integer $receive_start_time
 * @property integer $receive_end_time
 * @property integer $created_at
 * @property integer $updated_at
 */
class Coupon extends \yii\db\ActiveRecord
{
    const STATUS_READY = 10;
    const STATUS_BEGIN = 20;
    const STATUS_END = 30;

    public static $statusArray = [
        self::STATUS_READY => '准备中',
        self::STATUS_BEGIN => '已开始',
        self::STATUS_END => '已结束',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['platform_id', 'dealer_id', 'name', 'short_name', 'image', 'type', 'number', 'amount', 'effective_way', 'start_time', 'end_time', 'upper_limit', 'superposition', 'tips', 'desc', 'status', 'receive_start_time', 'receive_end_time', 'created_at', 'updated_at'], 'required'],
            [['platform_id', 'dealer_id', 'type', 'number', 'effective_way', 'start_time', 'end_time', 'upper_limit', 'superposition', 'status', 'receive_start_time', 'receive_end_time', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['name', 'short_name', 'image', 'desc'], 'string', 'max' => 255],
            [['tips'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'platform_id' => '平台',
            'dealer_id' => '经销商（商家）,第三方经销商\\自营id为0',
            'name' => '名称',
            'short_name' => '短名称',
            'image' => '图片',
            'type' => '类型；优惠券、折扣券',
            'number' => '库存数量',
            'amount' => '券的价格面额',
            'effective_way' => '有效方式；固定生效，领取后生效',
            'start_time' => '生效起始时间;当为生效类型是固定日期时，为时间戳；当生效类型为领取后生效时，为天数',
            'end_time' => '生效结束时间;当为生效类型是固定日期时，为时间戳；当生效类型为领取后生效时，为天数',
            'upper_limit' => '单个用户领取上限',
            'superposition' => '是否叠加使用',
            'tips' => '使用提示',
            'desc' => '其他说明',
            'status' => '状态;10：准备中；20、已开始；30、已结束',
            'receive_start_time' => '可领取的开始时间',
            'receive_end_time' => '可领取的最后时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }


}
