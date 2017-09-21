<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/21
 * Time: 下午1:33
 */

namespace common\helpers;

/**
 * 处理model的错误信息
 * Class ModelError
 * @package common\helpers
 */
class ModelError
{
    /**
     * 生成P标签的html文本
     * @param $modelErrors
     * @return string
     */
    public static function htmlP($modelErrors)
    {
        $html = '';
        foreach ($modelErrors as $errors) {
            foreach ($errors as $error) {
                $html .= "<p>{$error}</p>";
            }
        }
        return $html;
    }
}