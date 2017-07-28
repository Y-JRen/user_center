<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/29
 * Time: 9:41
 */

namespace passport\modules\inside\controllers;


use common\models\SystemConf;
use yii\helpers\ArrayHelper;

class RateController extends BaseController
{
    public function verbs()
    {
        return [
            'info' => ['GET']
        ];
    }

    /**
     * ratio 手续费的百分比
     * capped 封顶金额，0没有上限
     */
    public function actionInfo()
    {
        $model = SystemConf::find()->where(['key' => 'rate'])->one();
        $data = json_decode(ArrayHelper::getValue($model, 'value', ''), true);
        $info = ArrayHelper::getValue($data, 'info');

        $infoArr = [];
        foreach ($info as $key => $value) {
            $value['is_modify_rate'] = (bool)ArrayHelper::getValue($value, 'is_modify_rate');
            if (ArrayHelper::getValue($value, 'is_show')) {
                unset($value['is_show']);
                $infoArr[] = $value;
            }
        }

        $result = [
            'info' => $infoArr,
            'default_checked' => ArrayHelper::getValue($data, 'default_checked'),// 默认借记卡
            'is_modify' => (bool)ArrayHelper::getValue($data, 'is_modify')// m默认不允许修改
        ];

        return $this->_return($result);
    }
}