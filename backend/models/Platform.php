<?php

namespace backend\models;

class Platform extends \common\models\Platform
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'name_cn'], 'required'],
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
            'name' => '英文名',
            'name_cn' => '中文名',
            'token' => 'Token',
            'allow_ips' => '允许的IP',
            'callback_domain' => '回调域名',
        ];
    }
}
