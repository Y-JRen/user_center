<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "system_conf".
 *
 * @property string $key
 * @property string $label
 * @property string $value
 * @property string $type
 * @property string $remark
 * @property integer $disabled
 * @property integer $is_show
 */
class SystemConf extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'system_conf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'label', 'value'], 'required'],
            [['value'], 'string'],
            [['disabled', 'is_show'], 'integer'],
            [['key'], 'string', 'max' => 100],
            [['label', 'type', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Key',
            'label' => 'Label',
            'value' => 'Value',
            'type' => 'Type',
            'remark' => 'Remark',
            'disabled' => 'Disabled',
            'is_show' => 'Is Show',
        ];
    }

    /**
     * 通过key获取配置值
     * @todo 可优化使用redis缓存
     *
     * @param $key
     * @return false|null|string
     */
    public static function getValue($key)
    {
        return self::find()->select('value')->where(['key' => $key])->asArray()->scalar();
    }
}
