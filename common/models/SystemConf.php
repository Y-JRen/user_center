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
class SystemConf extends \yii\db\ActiveRecord
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
            [['key', 'label', 'type', 'remark'], 'string', 'max' => 255],
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
}
