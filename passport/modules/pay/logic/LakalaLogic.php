<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/27
 * Time: 15:44
 */

namespace passport\modules\pay\logic;


use passport\logic\Logic;
use yii\helpers\Url;

class LakalaLogic extends Logic
{
    /**
     * 生成拉卡拉的二维码
     *
     * @param $order
     * @return array
     */
    public function pay($order)
    {
        return [
            'data' => [
                'qrCodeData' => $order->order_id,
                'qrcode' => Url::to(['/default/qrcode', 'url' => $order->order_id], true),
            ],
            'status' => 0
        ];
    }
}