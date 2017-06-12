<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log_api".
 *
 * @property integer $id
 * @property string $url
 * @property string $param
 * @property string $method
 * @property string $ip
 * @property string $created_at
 */
class LogApi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_api';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['param'], 'required'],
            [['param'], 'string'],
            [['created_at'], 'safe'],
            [['url'], 'string', 'max' => 255],
            [['method'], 'string', 'max' => 8],
            [['ip'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => '请求链接',
            'param' => '请求参数',
            'method' => '请求方式',
            'ip' => '请求IP',
            'created_at' => '请求时间',
        ];
    }
}
