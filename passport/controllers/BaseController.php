<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 17:51
 */

namespace passport\controllers;

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
        if (!YII_ENV_DEV) {
            $get = Yii::$app->request->get();
            $post = Yii::$app->request->post();
            $params = array_merge($get, $post);

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
    }


    /**
     * 错误码定义
     * @var array
     */
    public static $errorStatuses = [
        //充值类错误
        2001 => '充值订单创建失败',
        2002 => '微信支付下单失败',
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