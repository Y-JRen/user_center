<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/5
 * Time: 14:31
 */
namespace common\logic;


use yii\helpers\FileHelper;
/**
 * api 接口日志
 * Class ApiLogsLogic
 * @package api\logic
 */
class ApiLogsLogic extends Logic
{
    /**
     * 写日志
     * @param $fileName
     * @param $data
     */
    public function addLogs($fileName, $data)
    {
        $filePath = $this->getFilePath($fileName);
        $this->writeLogs($filePath, $data);
    }
    /**
     * 写日志
     * @param $filePath
     * @param $data
     */
    public function addLogging($filePath, $data)
    {
        $this->writeLogs($filePath, $data);
    }
    /**
     * 获取文件路径
     * @param $fileName
     * @return string
     */
    private function getFilePath($fileName)
    {
        return \Yii::$app->getRuntimePath() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . date("Y-m") . DIRECTORY_SEPARATOR . date('d') . DIRECTORY_SEPARATOR . $fileName;
    }
    /**
     * 写日志
     * @param $filePath
     * @param $data
     * @return bool
     */
    private function writeLogs($filePath, $data)
    {
        if (!is_dir($filePath)) {
            @FileHelper::createDirectory(dirname($filePath));
        }
        if (!empty($data)) {
            $fp = @fopen($filePath, 'ab');
            if ($fp) {
                $data  = json_encode($data);
                @flock($fp, LOCK_EX);
                fwrite($fp, $data . PHP_EOL);
                @flock($fp, LOCK_UN);
                @fclose($fp);
            } else {
                return false;
            }
        }
        return false;
    }
}