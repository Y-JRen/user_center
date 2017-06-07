<?php
namespace passport\helpers;

use yii;

class Token
{
    /**
     * 加密token
     */
    public static function encodeToken($user_id,$platform, $client)
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
     */
    public static function createToken($user_id)
    {
    	$time_out = 3600;
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
    
    public static function delToken($token)
    {
    	$redis = yii::$app->redis;
    	$token_info = $redis->get($token);
    	$tmp = explode('_', $token_info);
    	$redis->del($token);
    	$redis->del("LOGIN_TOKEN:{$tmp[0]}:{$tmp[1]}_{$tmp[2]}");
    }
    
    public static function getToken($token)
    {
    	$redis = yii::$app->redis;
    	$tmp = $redis->get($token);
    	$res = json_decode($tmp,1);
    	return $res;
    }
    
}