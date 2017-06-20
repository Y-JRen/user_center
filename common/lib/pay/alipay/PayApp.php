<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/7
 * Time: 18:26
 *
 * 支付宝wap支付模块
 */

namespace common\lib\pay\alipay;


class PayApp
{

    // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
    private $body;

    // 订单标题，粗略描述用户的支付目的。
    private $subject;

    // 商户订单号.
    private $outTradeNo;

    // (推荐使用，相对时间) 支付超时时间，5m 5分钟
    private $timeExpress;

    // 订单总金额，整形，此处单位为元，精确到小数点后2位，不能超过1亿元
    private $totalAmount;

    // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
    private $sellerId;

    // 产品标示码，固定值：QUICK_WAP_PAY
    private $productCode;

    private $bizContentarr = array();

    private $bizContent = NULL;

    // 可用渠道设置
    private $enable_pay_channels;

    public function getBizContent()
    {
        if(!empty($this->bizContentarr)){
            $this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
        }
        return $this->bizContent;
    }

    public function __construct()
    {
        $this->bizContentarr['productCode'] = "QUICK_MSECURITY_PAY";
    }

    public function AlipayTradeWapPayContentBuilder()
    {
        $this->__construct();
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
        $this->bizContentarr['body'] = $body;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        $this->bizContentarr['subject'] = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
        $this->bizContentarr['out_trade_no'] = $outTradeNo;
    }

    public function setTimeExpress($timeExpress)
    {
        $this->timeExpress = $timeExpress;
        $this->bizContentarr['timeout_express'] = $timeExpress;
    }

    public function getTimeExpress()
    {
        return $this->timeExpress;
    }

    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
        $this->bizContentarr['total_amount'] = $totalAmount;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
        $this->bizContentarr['seller_id'] = $sellerId;
    }

    public function getSellerId()
    {
        return $this->sellerId;
    }

    public function setEnablePayChannels()
    {
        $enable_pay_channels = "balance,moneyFund,coupon,pcredit,pcreditpayInstallment,debitCardExpress,mcard,pcard,bankPay";
        $this->enable_pay_channels = $enable_pay_channels;
        $this->bizContentarr['enable_pay_channels'] = $enable_pay_channels;
    }

    public function getEnablePayChannels()
    {
        return $this->enable_pay_channels;
    }
}