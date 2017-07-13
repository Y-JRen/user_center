<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/4
 * Time: 11:16
 */

namespace passport\modules\sso\models;


use yii\helpers\ArrayHelper;

class Coupon extends \common\models\Coupon
{
    public function fields()
    {
        $data = parent::fields();
        $data['statusName'] = function () {
            return ArrayHelper::getValue(self::$statusArray, $this->status);
        };
        return $data;
    }
}