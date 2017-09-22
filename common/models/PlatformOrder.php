<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "platform_order".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $platform_order_id
 * @property string $platform_order_no
 * @property string $pro_type
 * @property string $pro_name
 * @property integer $status
 * @property integer $create_time
 * @property integer $created_at
 * @property integer $updated_at
 */
class PlatformOrder extends BaseModel
{
    public static $statusArray = [
        0 => '已保存',
        10 => '待付定金',
        11 => '已付定金',
        20 => '待销售确认',
        21 => '销售已确认',
        30 => '待付尾款',
        31 => '已付尾款',
        99 => '订单完成',
        -1 => ' 订单失败'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'platform_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'platform_order_id', 'pro_type', 'pro_name', 'status', 'create_time'], 'required'],
            [['uid', 'platform_order_id', 'create_time', 'created_at', 'updated_at', 'status'], 'integer'],
            [['pro_type', 'pro_name', 'platform_order_no'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户ID',
            'platform_order_id' => '电商订单ID',
            'platform_order_no' => '平台订单号',
            'pro_type' => '商品类型',
            'proTypeName' => '商品类型',
            'pro_name' => '商品名称',
            'status' => '状态',
            'statusName' => '状态',
            'platform' => '平台',
            'create_time' => '订单时间',
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

    public function getStatusName()
    {
        return ArrayHelper::getValue(self::$statusArray, $this->status);
    }

    public function getProTypeName()
    {
        return '新车';
    }

    public function getPlatform()
    {
        return '电商';
    }
}
