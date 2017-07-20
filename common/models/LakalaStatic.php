<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lakala_static".
 *
 * @property integer $id
 * @property integer $pos_id
 * @property string $version
 * @property integer $created_at
 */
class LakalaStatic extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lakala_static';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pos_id', 'version', 'created_at'], 'required'],
            [['created_at'], 'integer'],
            [['pos_id', 'version'], 'string', 'max' => 32],
        ];
    }
}
