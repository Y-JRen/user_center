<?php

namespace common\lib;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class Upload
{
    /**
     * @param $model ActiveRecord
     * @param $name
     * @return bool
     */
    public static function file($model, $name)
    {
        $oldName = $model->getOldAttribute($name);
        $uploadedFile = UploadedFile::getInstance($model, $name);
        if (is_null($uploadedFile)) {
            $model->setAttribute($name, $oldName);
            return false;
        }

        if ($uploadedFile->hasError) {
            switch ($uploadedFile->error) {
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            $model->addError($name, $error);
            return false;
        }

        $ymd = '/' . rand(10, 20) . '/' . rand(10, 20) . '/' . rand(10, 20) . '/';
        $save_path = \Yii::getAlias('@uploads') . $ymd;

        if (!file_exists($save_path)) {
            self::recursionMkDir($save_path);
        }

        $file_name = time() . rand(1000, 9999) . '.' . $uploadedFile->getExtension();

        if ($uploadedFile->saveAs($save_path . $file_name)) {
            \Yii::error('1111111111111111111111');
            $model->setAttribute($name, Url::to('/uploads' . $ymd . $file_name, true));

            self::deleteOldFile($oldName);
            return true;
        }

        return false;
    }

    /**
     * 删除旧文件
     * @param $fileUrl
     * @return bool
     */
    public static function deleteOldFile($fileUrl)
    {
        if (empty($fileUrl)) {
            return true;
        }

        $data = parse_url($fileUrl);
        $path = ArrayHelper::getValue($data, 'path');
        if (empty($path)) {
            return true;
        }

        $filePath = dirname(\Yii::getAlias('@uploads')) . $path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        return true;
    }

    public static function getPath($fileName)
    {
        return \Yii::getAlias('@uploads' . $fileName);
    }

    /**
     * 递归创建目录
     * @param $dir string 要创建的目录
     */
    private static function recursionMkDir($dir)
    {
        if (!is_dir($dir)) {
            if (!is_dir(dirname($dir))) {
                self::recursionMkDir(dirname($dir));
                mkdir($dir);
                chmod($dir, 0777);
            } else {
                mkdir($dir);
                chmod($dir, 0777);
            }
        }
    }
}