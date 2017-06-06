<?php
namespace passport\helpers;

class Token
{
    /**
     * 生成token
     */
    public static function encodeToken($user_id,$time)
    {
        return md5(123);
    }
    
}