<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "log_review".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $order_id
 * @property integer $order_status
 * @property string $remark
 * @property integer $created_at
 */
class LogReview extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_review';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'order_status'], 'required'],
            [['id', 'admin_id', 'order_id', 'order_status', 'created_at'], 'integer'],
            [['remark'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => '操作员id',
            'order_id' => '订单id',
            'order_status' => '订单状态',
            'remark' => '备注',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 获取订单状态
     * @param null $key
     * @return array|mixed
     */
    public static function getStatus($key = null)
    {
        $data = [
            Order::STATUS_SUCCESSFUL => '处理通过',
            Order::STATUS_FAILED => '处理不通过',
        ];

        if (is_null($key)) {
            return $data;
        } else {
            return ArrayHelper::getValue($data, $key);
        }
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            //            $this->admin_id = Yii::$app->user->id;
            $this->admin_id = 1;
            $this->created_at = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }
}
