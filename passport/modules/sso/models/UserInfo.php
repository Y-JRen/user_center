<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/13
 * Time: 14:19
 */

namespace passport\modules\sso\models;


class UserInfo extends \common\models\UserInfo
{
    const SCENARIO_REAL = 'real';

    public function rules()
    {
        $data = parent::rules();
        $data[] = [['real_name', 'card_number'], 'required'];
        $data[] = ['real_name', 'validatorRealName'];
        return $data;
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [],
            self::SCENARIO_REAL => ['uid', 'real_name', 'card_number', 'is_real', 'created_at', 'updated_at'],
        ];
    }

    public function validatorRealName()
    {
        if ($this->is_real == 1) {
            $this->addError('real_name', '该用户已经实名认证成功');
            return false;
        }

        return $this->_verify();
    }

    protected function _verify()
    {
        $pattern_1 = '/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/';
        $pattern_2 = '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/';
        if (!preg_match($pattern_1, $this->card_number) && !preg_match($pattern_2, $this->card_number)) {
            $this->addError('card_number', '身份证号码不正确');
            return false;
        }

        $this->is_real = 1;// 设置该用户已实名成功
        return true;
    }

    public function fields()
    {
        return [
            'uid',
            'is_real',
            'real' => function ($model) {
                return $model->is_real == 1 ? '已认证' : '未认证';
            },
            'real_name',
            'card_number',
            'birthday',
            'sex',
            'area',
            'city',
            'county'
        ];
    }
}