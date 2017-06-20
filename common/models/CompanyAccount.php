<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "company_account".
 *
 * @property integer $id
 * @property integer $type
 * @property string $card_bumber
 * @property string $bank_name
 * @property string $branch_name
 * @property integer $created_at
 * @property integer $updated_at
 */
class CompanyAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['card_bumber'], 'string', 'max' => 50],
            [['bank_name', 'branch_name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '账号类型',
            'card_bumber' => '卡号',
            'bank_name' => '银行名称',
            'branch_name' => '支行信息',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }
    
    public static function dropList($type = 0)
    {
        if($type) {
            $data = self::find()->where(['type' => $type])->all();
        } else {
            $data = self::find()->all();
        }
        $return = [];
        foreach ($data as $v) {
            $return[$v->id] = $v->branch_name. '-'. $v->card_bumber;
        }
        return $return;
    }
}
