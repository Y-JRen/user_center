<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin_user".
 *
 * @property integer $id
 * @property string $name
 * @property integer $org_id
 * @property integer $is_delete
 * @property string $profession
 * @property string $email
 * @property string $phone
 * @property string $access_token
 * @property integer $last_login_time
 * @property string $bqq_open_id
 * @property string $role_ids
 */
class AdminUser extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'org_id'], 'required'],
            [['id', 'org_id', 'is_delete', 'last_login_time'], 'integer'],
            [['name', 'email', 'bqq_open_id', 'role_ids'], 'string', 'max' => 255],
            [['profession'], 'string', 'max' => 4],
            [['phone'], 'string', 'max' => 11],
            [['access_token'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '员工id',
            'name' => '员工姓名',
            'org_id' => '员工所在的组织id',
            'is_delete' => '员工是否被删除：0 - 正常1 - 删除',
            'profession' => '员工职位',
            'email' => '员工邮箱',
            'phone' => '员工手机号',
            'access_token' => '员工登录的token',
            'last_login_time' => '员工最近一次登录时间',
            'bqq_open_id' => '员工企业QQ的信息',
            'role_ids' => '员工角色id',
        ];
    }
    
    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }
    
    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return false;
    }
    
    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return '';
    }
    
    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }
}
