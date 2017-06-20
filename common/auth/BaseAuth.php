<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 11:40
 */

namespace common\auth;


use common\models\User;
use passport\helpers\Token;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class BaseAuth extends QueryParamAuth
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'token';

    /**
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     * @return bool |object
     * @throws HttpException
     */
    public function authenticate($user, $request, $response)
    {
        $userToken = $request->get($this->tokenParam);
        if (!$userToken) {
            $userToken = $request->post($this->tokenParam);
        }
        if ($userToken) {
            $data = Token::getToken($userToken);
            if (!$data) {
                throw new HttpException(401, 'USER_TOKEN失效', 401);
            }
            $uid = ArrayHelper::getValue($data, 'uid');
            $identity = $user->login(User::findOne($uid), get_class($this));
            if ($identity !== null) {
                return $identity;
            }

        }
        throw new HttpException(403, '参数异常', 403);
    }
}