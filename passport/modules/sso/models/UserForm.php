<?php

namespace passport\modules\sso\models;


use common\models\User;
use yii\base\Model;
use passport\helpers\Token;
use passport\helpers\Config;
use yii;
use passport\logic\SmsLogic;
use passport\logic\ImgcodeLogic;

/**
 * Class UserForm
 *
 * @property integer $login_time
 *
 * @package passport\modules\sso\models
 */
class UserForm extends Model
{
    /**注册场景*/
    const SCENARIO_REG = 'reg';
    /**登入场景*/
    const SCENARIO_LOGIN = 'login';
    /**已登录场景*/
    const SCENARIO_LOGGED = 'logged';
    /**更改密码场景*/
    const SCENARIO_REPASSWD = 'repasswd';
    
    const QUICK_LOGIN = 'quick_login';

    public $token;
    public $user_name;
    public $passwd;
    public $repasswd;
    public $verify_code;
    public $channel;
    public $is_agreement;
    public $img_code;
    public $img_unique;
    public $uuid;
    

    public function rules()
    {
        return [
            [['user_name', 'passwd', 'repasswd', 'verify_code', 'channel', 'is_agreement'], 'required', 'on' => [self::SCENARIO_REG],],
            [['user_name', 'passwd', 'uuid'], 'required', 'on' => [self::SCENARIO_LOGIN],],
            ['token', 'required', 'on' => [self::SCENARIO_LOGGED],],
            [['user_name', 'passwd', 'repasswd', 'verify_code'], 'required', 'on' => [self::SCENARIO_REPASSWD],],
            [['img_code', 'img_unique'], 'required', 'on' => self::SCENARIO_LOGIN, 'when' => function ($model) {
                return $model->getLoginError() > 3;
            }],
            ['img_code', 'validateImgCode', 'on' => [self::SCENARIO_LOGIN]],
            ['user_name', 'match', 'pattern' => '/^1\d{10}/', 'message' => '手机号不正确'],
            ['user_name', 'unique', 'targetClass' => '\common\models\User', 'targetAttribute' => 'phone', 'on' => [self::SCENARIO_REG], 'message' => '手机号存在.'],
            ['passwd', 'string', 'length' => 32, 'message' => '密码格式错误'],
            ['repasswd', 'compare', 'compareAttribute' => 'passwd', 'message' => '两次输入的密码不一致'],
            ['verify_code', 'validateCode'],
            ['is_agreement', 'compare', 'compareValue' => 1, 'operator' => '==', 'message' => '必需同意协议'],
            ['token', 'validateToken'],
            [['user_name', 'verify_code', 'channel'], 'required', 'on' => [self::QUICK_LOGIN]]
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_REG => ['user_name', 'passwd', 'repasswd', 'verify_code', 'channel', 'is_agreement'],
            self::SCENARIO_LOGIN => ['user_name', 'passwd', 'uuid', 'img_code', 'img_unique'],
            self::SCENARIO_LOGGED => ['token'],
            self::SCENARIO_REPASSWD => ['user_name', 'passwd', 'repasswd', 'verify_code'],
            self::QUICK_LOGIN => ['user_name', 'verify_code', 'channel']
        ];
    }

    /**
     * 验证码验证
     * @param $attribute
     */
    public function validateCode($attribute)
    {
        $type = ($this->scenario == self::QUICK_LOGIN) ? 2 : 1;
        if (!$this->hasErrors()) {
            $bool = SmsLogic::instance()->checkCode($type, $this->$attribute, $this->user_name);
            if (!$bool) {
                $this->addError($attribute, '验证码错误！');
            }
        }
    }

    /**
     * 验证token
     *  @param $attribute
     */
    public function validateToken($attribute)
    {
        if (!$this->hasErrors()) {
            if (!Token::checkToken($this->$attribute)) {
                $this->addError($attribute, 'token不正确！');
            }
        }
    }

    /**
     * 验证图形验证码
     *
     * 1、登录场景错误超过三次需要验证
     *
     *
     * @param $attribute string
     *
     * @return bool
     */
    public function validateImgCode($attribute)
    {
        if ($this->scenario == self::SCENARIO_LOGIN) {
            $bool = ImgcodeLogic::instance()->checkImgCode($this->$attribute, $this->img_unique);
            if ($bool) {
                return true;
            } else {
                $this->addError($attribute, '图形验证码错误！');
                return false;
            }
        }

        return true;
    }

    /**
     * 1、用户名、密码不能为空
     * 2、验证是否需要图形验证码
     * 3、验证用户是否正确
     * 4、登录并生成token
     *
     * @param $user_id
     * @return string
     */
    public function login($user_id)
    {
        //create token
        $token = Token::createToken($user_id);
        $user = User::findOne($user_id);
        $user->login_time = time();
        $user->save();
        return $token;
    }

    public function reg()
    {
        $model = new User();
        $model->phone = $this->user_name;
        $model->user_name = $this->user_name;
        $model->email = '';
        $model->passwd = $this->encryptPassword($this->passwd);
        $model->from_platform = Config::getPlatform();
        $model->from_channel = $this->getFrom();
        $model->reg_time = time();
        $model->reg_ip = $this->getIp();
        $model->login_time = 0;
        if (!$model->insert()) {
            return ['status' => false, 'msg' => current($model->getErrors())[0]];
        } else {
            return ['status' => true, 'user_id' => $model->id];
        }
    }

    /**
     * 登入验证
     */
    public function checkLogin()
    {
        $model = new User();
        $user = $model::findOne(['phone' => $this->user_name]);
        if (!$user) {
            return ['status' => false, 'msg' => '用户不存在'];
        }
        if ($user->passwd != $this->encryptPassword($this->passwd)) {
            return ['status' => false, 'msg' => '密码不正确'];
        }
        return ['status' => true, 'user_id' => $user->id];
    }

    /**
     * 更改密码
     */
    public function changePassword()
    {
        $user_count = User::find()->where(['phone' => $this->user_name])->count();
        if ($user_count > 1) {
            return ['status' => false, 'msg' => '此手机号存在多个用户！'];
        } elseif ($user_count < 1) {
            return ['status' => false, 'msg' => '用户不存在！'];
        }
        $user = User::find()->where(['phone' => $this->user_name])->one();
        $user->passwd = $this->encryptPassword($this->passwd);
        if ($user->save()) {
            return ['status' => true];
        } else {
            return ['status' => false, 'msg' => $user->firstErrors];
        }
    }

    /**
     * 登出
     */
    public function logout()
    {
        Token::delToken($this->token);
        return true;
    }


    /**
     * 获取来源
     */
    protected function getFrom()
    {
        return $this->channel;
    }

    /**
     * 获取ip
     */
    protected function getIp()
    {
        return yii::$app->request->userIP;
    }


    /**
     * 密码加密
     * @param $password
     * @return string
     */
    protected function encryptPassword($password)
    {
        $salt = '$*I_$%@#Abc^!';
        return md5($password . $salt . substr($password, -6));
    }

    /**
     * 设置当前登录错误次数
     */
    public function setLoginError()
    {
        $redis = Yii::$app->redis;
        $redis->incr($this->getLoginErrorKey());
        $redis->expire($this->getLoginErrorKey(), time() + 3600);
    }

    /**
     * 获取当前用户登录错误次数的限制
     * @return mixed
     */
    public function getLoginError()
    {
        return Yii::$app->redis->get($this->getLoginErrorKey());
    }

    /**
     * 清空当前登录错误次数
     */
    public function delLoginError()
    {
        Yii::$app->redis->del($this->getLoginErrorKey());
    }

    /**
     * 获取当前用户登录错误的key
     * @return string
     */
    public function getLoginErrorKey()
    {
        return "LOGIN_ERROR:{$this->uuid}";
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => '手机号',
            'user_name' => '用户名',
            'email' => '邮箱',
            'passwd' => '密码',
            'status' => '状态',
            'from_platform' => '平台来源',
            'from_channel' => '渠道来源',
            'reg_time' => '注册时间',
            'reg_ip' => '注册IP',
            'login_time' => '最后登陆时间',
            'img_code' => '图形验证码',
            'img_unique' => '图形验证码',
            'repasswd' => '确认密码',
            'verify_code' => '短信验证码',
        ];
    }
    
    /**
     * 快捷登陆
     *
     * @return array
     */
    public function quickLogin()
    {
        //有用户直接登陆，没有注册登陆
        if($user = User::findOne(['phone' => $this->user_name])) {
            $token = $this->login($user->id);
            return [
                'token' => $token,
                'uid' => $user->id
            ];
        } else {
            $model = new User();
            $model->phone = $this->user_name;
            $model->user_name = $this->user_name;
            $model->from_platform = Config::getPlatform();
            $model->from_channel = $this->getFrom();
            $model->reg_time = time();
            $model->reg_ip = $this->getIp();
            $model->login_time = 0;
            if (!$model->save(false)) {
                return ['status' => false, 'msg' => current($model->getErrors())[0]];
            } else {
                $token = $this->login($model->id);
            }
        }
        return [
            'token' => $token,
            'uid' => $user->id
        ];
    }
}