<?php

namespace backend\models;

use common\models\AdminUser;

class LogReview extends \common\models\LogReview
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'order_status'], 'required'],
            [['id', 'admin_id', 'order_id', 'order_status', 'created_at'], 'integer'],
            [['remark'], 'string'],
            ['remark', 'required', 'when' => function ($model) {
                return $model->order_status == Order::STATUS_FAILED;
            }]
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'admin_id']);
    }
}
