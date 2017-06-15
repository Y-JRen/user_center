<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "admin_role".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $permissions
 */
class AdminRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'permissions'], 'required'],
            [['id'], 'integer'],
            [['permissions'], 'string'],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '角色名称',
            'slug' => '角色别名',
            'permissions' => '角色权限信息json数据',
        ];
    }
}
