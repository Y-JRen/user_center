<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/22
 * Time: 15:57
 */

namespace common\helpers;


use yii\helpers\ArrayHelper;

class JsonHelper
{
    /**
     * 银行卡备注信息处理
     * @param $remark
     * @return array
     */
    public static function BankHelper($remark)
    {
        $data = [];
        if ($remark) {
            $array = json_decode($remark, true);
            if (array_key_exists('bankName', $array)) {
                $data['bankName'] = ['label' => '银行名称', 'value' => $array['bankName']];
            }
            if (array_key_exists('bankCard', $array)) {
                $data['bankCard'] = ['label' => '银行卡号', 'value' => $array['bankCard']];
            }
            if (array_key_exists('accountName', $array)) {
                $data['accountName'] = ['label' => '转账人姓名', 'value' => $array['accountName']];
            }
            if (array_key_exists('transferDate', $array)) {
                $data['transferDate'] = ['label' => '转账日期', 'value' => $array['transferDate']];
            }
            if (array_key_exists('referenceNumber', $array)) {
                $data['referenceNumber'] = ['label' => '流水单号', 'value' => $array['referenceNumber']];
            }
            if (array_key_exists('payType', $array)) {
                $data['payType'] = ['label' => '转账类型', 'value' => $array['payType']];
            }
            if (array_key_exists('referenceImg', $array)) {
                $data['referenceImg'] = ['label' => '相关凭证'];
                if (is_array($array['referenceImg'])) {
                    $data['referenceImg']['value'] = $array['referenceImg'];
                } else {
                    $data['referenceImg']['value'][] = $array['referenceImg'];
                }
            }
        }

        return $data;
    }
}