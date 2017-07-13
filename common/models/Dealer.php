<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dealer".
 *
 * @property integer $id
 * @property integer $platform_id
 * @property integer $platform_dealer_id
 * @property string $name
 */
class Dealer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dealer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['platform_id', 'platform_dealer_id', 'name'], 'required'],
            [['platform_id', 'platform_dealer_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'platform_id' => '平台',
            'platform_dealer_id' => '平台的经销商ID',
            'name' => '经销商名称',
        ];
    }

    /**
     *  获取对应的平台
     * @return \yii\db\ActiveQuery
     */
    public function getPlatform()
    {
        return $this->hasOne(Platform::className(), ['id' => 'platform_id']);
    }
}
