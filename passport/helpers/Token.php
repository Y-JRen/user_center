<?php
namespace passport\helpers;

use yii;

class Token
{
    /**
     * 加密token
     */
    protected static function encodeToken($user_id,$platform, $client)
    {
        return md5($user_id.time().$platform.$client);
    }
    /**
     * 验证token
     * @param string $token
     * @return boolean
     */
    public static function checkToken($token)
    {
    	$plat = Config::getPlatform();//获取平台
    	$client = Config::getClientType();//获取客户端类型
    	
    	$token_data = static::getToken($token);
    	$redis = yii::$app->redis;
    	$_token = $redis->get("LOGIN_TOKEN:{$token_data['uid']}:{$plat}_{$client}");
    	return $_token == $token;
    }
    /**
     * 创建token
     * @param int $user_id
     * @return string
     */
    public static function createToken($user_id)
    {
    	$time_out = Config::$tokenExpire;
    	$plat = Config::getPlatform();//获取平台
    	$client = Config::getClientType();//获取客户端类型
    	
    	$token = static::encodeToken($user_id, $plat, $client);
    	
    	$redis = yii::$app->redis;
    	$redis->set("LOGIN_TOKEN:{$user_id}:{$plat}_{$client}", $token);
    	$redis->expire("LOGIN_TOKEN:{$user_id}:{$plat}_{$client}" , $time_out );
    	$data = ['uid'=>$user_id,'plat'=>$plat,'client'=>$client];
    	$redis->set($token,json_encode($data));
    	$redis->expire($token, $time_out );
    	
    	return $token;
    }
    /**
     * 删除token
     * @param string $token
     */
    public static function delToken($token)
    {
    	$redis = yii::$app->redis;
    	$token_info = static::getToken($token);
    	$redis->del("LOGIN_TOKEN:{$token_info['uid']}:{$token_info['plat']}_{$token_info['client']}");
    	$redis->del($token);
    }
    
    public static function getToken($token)
    {
    	$redis = yii::$app->redis;
    	$tmp = $redis->get($token);
    	$res = json_decode($tmp,1);
    	return $res;
    }

    public static function getUid($token)
    {
        return yii\helpers\ArrayHelper::getValue(static::getToken($token), 'uid');
    }
    
}