<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "platform_order".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $platform_order_id
 * @property integer $created_at
 */
class PlatformOrder extends BaseModel
{
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
            [['uid', 'platform_order_id', 'created_at'], 'required'],
            [['uid', 'platform_order_id', 'created_at'], 'integer'],
        ];
    }

}
