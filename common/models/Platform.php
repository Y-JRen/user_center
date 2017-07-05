<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "platform".
 *
 * @property integer $id
 * @property string $name
 * @property string $name_cn
 * @property string $token
 * @property string $allow_ips
 * @property string $callback_domain
 */
class Platform extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'platform';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'name_cn', 'token', 'allow_ips', 'callback_domain'], 'required'],
            [['name', 'name_cn', 'token'], 'string', 'max' => 100],
            [['allow_ips', 'callback_domain'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '英文名，不能有空格',
            'name_cn' => '中文名',
            'token' => 'Token',
            'allow_ips' => '允许的ip，多个ip，英文逗号隔开',
            'callback_domain' => '回调域名',
        ];
    }
}
