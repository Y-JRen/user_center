<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_oauth".
 *
 * @property string $id
 * @property string $open_id
 * @property integer $type
 * @property integer $uid
 * @property string $info
 * @property integer $created_at
 */
class UserOauth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_oauth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['open_id', 'type', 'uid', 'created_at'], 'required'],
            [['type', 'uid', 'created_at'], 'integer'],
            [['open_id'], 'string', 'max' => 32],
            [['info'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'open_id' => 'Open ID',
            'type' => 'Type',
            'uid' => 'Uid',
            'info' => 'Info',
            'created_at' => 'Created At',
        ];
    }
}
