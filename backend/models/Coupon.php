<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/5
 * Time: 13:49
 */

namespace backend\models;


class Coupon extends \common\models\Coupon
{
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
            [['platform_id', 'dealer_id', 'name', 'short_name', 'image', 'type', 'number', 'amount', 'effective_way', 'start_time', 'end_time', 'upper_limit', 'superposition', 'tips', 'status', 'receive_start_time', 'receive_end_time'], 'required'],
            [['platform_id', 'dealer_id', 'type', 'number', 'effective_way', 'upper_limit', 'superposition', 'status', 'created_at', 'updated_at'], 'integer'],
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
}