<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/12
 * Time: 15:24
 */

namespace common\jobs;

use Yii;
use common\models\LogApi;
use yii\base\Object;
use zhuravljov\yii\queue\Job;

/**
 * api 请求日志队列
 * Class ApiLogJob
 * @package common\jobs
 */
class ApiLogJob extends Object implements Job
{
    /**
     * 请求链接
     * @var
     */
    public $url;
    
    /**
     * 请求参数
     * @var
     */
    public $param;
    
    /**
     * 请求方法
     * @var
     */
    public $method;
    
    /**
     * 请求IP
     * @var
     */
    public $ip;
    
    /**
     * 请求时间
     * @var
     */
    public $created_at;
    
    public function execute($queue)
    {
        $logApi = new LogApi();
        $logApi->url = $this->url;
        $logApi->param = $this->param;
        $logApi->method = $this->method;
        $logApi->ip = $this->ip;
        $logApi->created_at = $this->created_at;
        if (!$logApi->save()) {
            //错误重新加入队列
            \Yii::$app->queue->push(new self([
                'url' => $this->url,
                'param' => $this->param,
                'method' => $this->method,
                'ip' => $this->ip,
                'created_at' => $this->created_at
            ]));
        }
    }
}