<?php

/* @var $this yii\web\View */

use yii\helpers\ArrayHelper;

$this->title = '车城网用户中心';
?>

<style type="text/css">
    * {
        padding: 0;
        margin: 0;
    }

    .user {
        padding: 24px;
    }

    .user-title {
        width: 100%;
        margin-bottom: 24px;
    }

    .user-title span {
        font-family: PingFangSC-Medium;
        font-size: 24px;
        color: #1A1A1A;
        letter-spacing: 0;
    }

    .mb-md {
        margin-bottom: 16px;
    }

    .Today {
        background: #FFFFFF;
        border: 1px solid #DCE0E0;
        Today-shadow: 0 1px 3px 0 rgba(48, 50, 70, 0.20);
        border-radius: 4px;
        margin-top: 30px;
    }

    .Today .Today-title {
        background: #00D6B1;
        height: 40px;
    }

    .Today .Today-title span {
        display: block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #FFFFFF;
        float: left;
        margin: 0 10px;
        margin-top: 13px;
    }

    .Today .Today-title strong {
        font-family: PingFangSC-Medium;
        font-size: 14px;
        color: #FFFFFF;
        letter-spacing: 0;
        padding: 10px;
    }

    .Today .Today-main {
        padding: 24px 0 0 30px;
        height: 100px;
    }

    .Today .user-new {
        float: left;
        width: 15%;
    }

    .Recharge-money, .consumption-money, .refund-money, .Withdrawals-money {
        float: left;
        width: 10%;
    }

    .Today .user-new .Today-img {
        margin-right: 16px;
        width: 58px;
        height: 58px;
        line-height: 58px;
        float: left;
        border-radius: 50%;
        text-align: center;
        font-size: 28px;
        background: #00D6B1;
        float: left;
    }

    .Today .user-new .Today-img img {
        position: relative;
        width: 36px;
        height: 36px;
        display: block;
        margin: 0 auto;
        margin-top: 12px;
    }

    .Today .user-new .data {
        float: left;
    }

    .Today .user-new .data span {
        display: block;
        width: 100%;
        font-family: PingFangSC-Regular;
        font-size: 14px;
        color: #666666;
        letter-spacing: 0;
        margin-bottom: 8px;
    }

    .Today .user-new .data strong {
        font-family: Roboto-Bold;
        font-size: 28px;
        color: #1A1A1A;
        letter-spacing: 0;
        display: block;
        width: 100%;
    }

    .user-new {
        margin-right: 7.6%
    }

    .Recharge-money {
        margin-right: 5.7%
    }

    .consumption-money {
        margin-right: 6.8%
    }

    .refund-money {
        margin-right: 8.9%
    }

    .Withdrawals-money {
        margin-right: 4.2%
    }

    .Today span {
        display: block;
        width: 100%;
        font-family: PingFangSC-Regular;
        font-size: 14px;
        color: #666666;
        letter-spacing: 0;
        margin-bottom: 8px;
    }

    .Today strong {
        font-family: Roboto-Bold;
        font-size: 18px;
        color: #1A1A1A;
        letter-spacing: 0;
        display: block;
        width: 100%;
    }

    .Yesterday-color {
        background: #03A1FF !important;
    }
</style>
<?php foreach ($data as $key => $value): ?>
    <div class="Today <?= ($key == 0) ? 'mb-md' : ''; ?>">
        <div class="Today-title <?= ($key == 0) ? '' : 'Yesterday-color' ?>">
            <span></span>
            <strong><?= ($key == 0) ? '今天' : '昨天' ?></strong>
        </div>
        <div class="Today-main">
            <div class="user-new">
                <div class="Today-img <?= ($key == 0) ? '' : 'Yesterday-color' ?>">
                    <img src="/img/status_avatar.png">
                </div>

                <div class="data">
                    <span>新用户</span>
                    <strong><?= ArrayHelper::getValue($value, 'user', 0) ?></strong>
                </div>
            </div>
            <div class="Recharge-money">
                <span>充值金额(元)</span>
                <strong><?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($value, 'recharge', 0)); ?></strong>
            </div>
            <div class="consumption-money">
                <span>消费金额(元)</span>
                <strong><?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($value, 'consume', 0)); ?></strong>
            </div>
            <div class="refund-money">
                <span>退款金额(元)</span>
                <strong><?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($value, 'refund', 0)); ?></strong>
            </div>
            <div class="Withdrawals-money">
                <span>提现金额(元)</span>
                <strong><?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($value, 'cash', 0)); ?></strong>
            </div>
        </div>
    </div>
<?php endforeach; ?>

