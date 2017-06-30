<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/29
 * Time: 9:41
 */

namespace passport\modules\inside\controllers;


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
        $data = [
            'info' => [
                ['type' => 'debit_card', 'ratio' => 0.5, 'capped' => 20, 'label' => '借记卡'],// 借记卡
                ['type' => 'credit_card', 'ratio' => 0.6, 'capped' => 0, 'label' => '信用卡'],// 信用卡
                ['type' => 'outside_card', 'ratio' => 2, 'capped' => 0, 'label' => '境外卡'],// 境外卡
            ],
            'default_checked' => 'debit_card',// 默认借记卡
            'is_modify' => false// m默认不允许修改
        ];

        return $this->_return($data);
    }
}