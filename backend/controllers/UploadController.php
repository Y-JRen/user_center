<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/22
 * Time: 下午5:59
 */

namespace backend\controllers;


use common\logic\OssLogic;
use Yii;
use yii\helpers\ArrayHelper;

class UploadController extends BaseController
{
    /**
     * @param $name string 字段名
     * @return array
     */
    public function actionFile($name = 'file')
    {
        Yii::$app->response->format = 'json';
        $result = ['status' => false];
        $file = ArrayHelper::getValue($_FILES, [$name, 'tmp_name', 'file', '0']);
        if ($file) {
            $suffixName = end(explode('.', ArrayHelper::getValue($_FILES, [$name, 'name', 'file', '0'], '')));
            $saveName = 'user_center/' . rand(11, 22) . '/' . rand(11, 22);
            $saveName .= ($suffixName ? time() . '/.' . $suffixName : '');

            $result['status'] = true;
            $result['url'] = OssLogic::instance()->uploadImgToOss($file, $saveName);
        }
        return $result;
    }

    public function actionDelete()
    {
        Yii::$app->response->format = 'json';
        $result = ['status' => true];
        $url = Yii::$app->request->post('key');
        if ($url) {
            $result['url'] = $url;
        } else {
            $result['status'] = false;
        }
        return $result;
    }
}