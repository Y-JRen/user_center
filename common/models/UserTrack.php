<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_track".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $platform
 * @property string $platform_type
 * @property string $desc
 * @property string $url
 * @property string $ip
 * @property integer $created_at
 */
class UserTrack extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_track';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'platform', 'platform_type', 'desc', 'url', 'ip', 'created_at'], 'required'],
            [['uid', 'platform', 'created_at'], 'integer'],
            [['platform_type'], 'string', 'max' => 20],
            [['desc', 'url'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'platform' => 'Platform',
            'platform_type' => 'Platform Type',
            'desc' => 'Desc',
            'url' => 'Url',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }
}
