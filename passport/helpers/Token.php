<?php
namespace passport\helpers;

class Token
{
    /**
     * 生成token
     */
    public static function encodeToken($user_id,$time,$platform, $client)
    {
        return md5($user_id.$time.$platform.$client);
    }
    
}