<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 17:51
 */

namespace passport\controllers;

use common\jobs\ApiLogJob;
use passport\helpers\Config;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\Response;

class BaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ],
            ],
        ];
    }

    /**
     * 验证IP、以及token是否正确
     * @throws HttpException
     */
    public function init()
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $params = array_merge($get, $post);
        if (!YII_ENV_DEV) {
            $domain = ArrayHelper::getValue($params, 'domain');
            if (empty($domain)) {
                throw new HttpException(401, '参数不正确', -999);
            }

            $domainInfo = Config::params($domain);
            if (empty($domainInfo)) {
                throw new HttpException(401, '参数不正确', -998);
            }

            $requestIp = Yii::$app->request->getUserIP();
            if (!in_array('*', $domainInfo['allowIps']) && !in_array($requestIp, $domainInfo['allowIps'])) {
                throw new HttpException(401, '该IP不在允许范围内', -997);
            }

            $accessToken = ArrayHelper::getValue($params, 'access_token');
            if (empty($accessToken)) {
                throw new HttpException(401, '参数不正确', -996);
            }

            unset($params['access_token']);
            ksort($params);
            $verifyToken = md5(http_build_query($params) . $domainInfo['tokenKey']);
            if ($verifyToken != $accessToken) {
                throw new HttpException(401, '参数不正确', -995);
            }
        }
        Yii::$app->queue->push(new ApiLogJob([
            'url' => Yii::$app->request->pathInfo,
            'param' => json_encode(['post' => $post , 'get' => $get]),
            'method' => Yii::$app->request->method,
            'ip' => Yii::$app->request->getUserIP(),
            'created_at' => date('Y-m-d H:i:s')
        ]));
    }


    /**
     * 错误码定义
     * @var array
     */
    public static $errorStatuses = [
        //实名认证
        990 => '参数不正确',
        991 => '认证失败',
        //发短信
    	997 => '发送失败',
    	998 => '发送次数超过限制',
    	999 => '手机号不正确',
        //登录注册
    	1001 => '参数错误',
    	1002 => '注册失败',
    	1003 => '帐号密码不正确',
    	1004 => '登入失败',
    	1005 => '验证失败',
    	1006 => '更改密码失败',	
        //充值类错误
        2001 => '充值订单创建失败',
        2101 => ' 消费订单创建失败',
        2201 => ' 退款订单创建失败',
        2301 => ' 提现订单创建失败',
        2002 => '微信支付下单失败',
        2003 => '支付宝支付下单失败',
        2004 => '支付宝充值异常',
        2005 => '异常订单',
        2006 => 'jsapi支付openid 必填',
        2007 => '参数异常',
        2010 => '充值参数有误',
    ];

    /**
     * 统一返回格式
     *
     * @param string|array|object $data 返回内容
     * @param string $message
     * @param int $code
     * @return array
     */
    public function _return($data, $code = 0, $message = 'success')
    {
        $message = (!$message) ? static::$errorStatuses[$code] : $message;
        return compact('data', 'message', 'code');
    }

    /**
     * 错误信息返回
     * @param int $code
     * @param string|array|object $data
     * @return array
     */
    public function _error($code, $data = null)
    {
        return $this->_return($data, $code, null);
    }
}