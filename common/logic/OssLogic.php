<?php
/**
 * 阿里云oss插件 logic.
 * User: 雕
 * Date: 2017/9/13
 * Time: 16:47
 */

namespace common\logic;

use Yii;
use OSS\OssClient;
use OSS\Core\OssException;

class OssLogic extends Logic
{
    private $ossClient = '';//oss客户端对象

    public function init()
    {
        try {
            $accessKeyId = Yii::$app->params['aliyun']['accessKeyId'];
            $accessKeySecret = Yii::$app->params['aliyun']['accessKeySecret'];
            $endpoint = Yii::$app->params['aliyun']['ossEndpoint'];
            $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
        } catch (OssException $e) {
            // 错误处理
        }
    }


    /**
     * @param string $strUploadImage        需要上传的文件名称，本地的 例如 ： E:/pic/a.png
     * @param string $strSaveName           保存在oss上面的文件名，不能以 /开头 例如 ： wangdiao/test/a.png，
     * @return string                       上传成功的话为cdn地址，失败的话为空字符串
     */
    public function uploadImgToOss($strUploadImage, $strSaveName)
    {
        $cdnUrl = '';
        if ($this->ossClient instanceof OssClient) {
            try {
                $imgBucket = Yii::$app->params['aliyun']['bucket'];
                $cdnDomain = Yii::$app->params['aliyun']['cdnDomain'];
                $options = array(
                    OssClient::OSS_CHECK_MD5 => true, //上传完后精选MD5校验，确保上传成功
                );
                $strSaveName = 'dianshangtob/' . $strSaveName;//tob项目的根目录固定
                $this->ossClient->uploadFile($imgBucket, $strSaveName, $strUploadImage, $options);
                $cdnUrl = "http://{$cdnDomain}/$strSaveName";
            } catch (OssException $e) {
                //错误处理
                $cdnUrl = '';
            }
        }
        return $cdnUrl;
    }

}