<?php

namespace common\models;

use passport\helpers\Token;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $phone
 * @property string $user_name
 * @property string $email
 * @property string $passwd
 * @property integer $status
 * @property integer $from_platform
 * @property string $from_channel
 * @property integer $reg_time
 * @property string $reg_ip
 * @property integer $login_time
 * @property string $client_type
 */
class User extends BaseModel implements IdentityInterface
{
    //用户状态
    const STATUS_NORMAL = 1;// 正常状态
    const STATUS_SEAL = 2;// 封禁状态
    const STATUS_SEAL_FOREVER = 3;// 永久封禁状态

    public static $statusArray = [
        self::STATUS_NORMAL => '正常状态',
        self::STATUS_SEAL => '封禁状态',
        self::STATUS_SEAL_FOREVER => '永久封禁状态',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'from_platform', 'reg_time', 'login_time'], 'required'],
            [['status', 'from_platform', 'reg_time', 'login_time'], 'integer'],
            [['phone'], 'string', 'max' => 12],
            [['user_name'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 20],
            [['passwd', 'client_type'], 'string', 'max' => 50],
            [['from_channel'], 'string', 'max' => 128],
            [['reg_ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => '手机号码',
            'user_name' => '用户名',
            'email' => '邮箱',
            'passwd' => 'Passwd',
            'status' => '状态',
            'from_platform' => '平台来源',
            'from_channel' => '渠道来源',
            'reg_time' => '注册时间',
            'reg_ip' => '注册IP',
            'login_time' => '最后登陆时间',
            'client_type' => '客户端类型',
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
     * $token 存在redis里
     *
     * @param mixed $token
     * @param null $type
     * @return IdentityInterface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $id = Token::getUid($token);
        return static::findIdentity($id);
    }

    /**
     * 获取UID
     *
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
        return true;
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
        return true;
    }

    /**
     * 获取用户余额
     * @return \yii\db\ActiveQuery | UserBalance
     */
    public function getBalance()
    {
        return $this->hasOne(UserBalance::className(), ['uid' => 'id']);
    }

    /**
     * 获取用户冻结余额
     * @return \yii\db\ActiveQuery | UserFreeze
     */
    public function getFreeze()
    {
        return $this->hasOne(UserFreeze::className(), ['uid' => 'id']);
    }

    /**
     * 获取用户扩展信息
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['uid' => 'id']);
    }
}
