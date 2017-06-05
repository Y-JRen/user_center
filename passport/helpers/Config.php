<?php

namespace passport\helpers;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * 相关项目配置
 * Class Config
 * @package passport\helpers
 */
class Config
{
    public static function params($domain = null)
    {
        $params = ArrayHelper::getValue(Yii::$app->params, 'projects');
        if (empty($domain)) {
            return $params;
        } else {
            return ArrayHelper::getValue($params, $domain);
        }

    }
}