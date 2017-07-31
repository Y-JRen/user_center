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
            [['order_id', 'order_status', 'remark'], 'required'],
            [['id', 'admin_id', 'order_id', 'order_status', 'created_at'], 'integer'],
            [['remark'], 'string'],
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'admin_id']);
    }
}
