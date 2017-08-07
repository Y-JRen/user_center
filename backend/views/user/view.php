<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '会员详情';
$this->params['breadcrumbs'][] = $this->title;

$balance = ArrayHelper::getValue($userModel->balance, 'amount', 0);
$freeze = ArrayHelper::getValue($userModel->freeze, 'amount', 0);
?>

<?= $this->render('_top_header'); ?>

<div class="order-index">
    <div class="box-body no-padding" style="background-color: #fff;">
        <table class="table" style="border-bottom: 1px solid #ddd">
            <tbody>
            <tr>
                <td>用户ID：</td>
                <td><?= $userModel->id ?></td>
                <td>会员昵称：</td>
                <td>--</td>
                <td>用户手机：</td>
                <td><?= $userModel->phone ?></td>
            </tr>
            <tr>
                <td>实名认证：</td>
                <td>
                    <?php if (is_object($data)) {
                        if($data->is_real==1){
                            echo '认证';
                        };
                    } else {
                        echo '--';
                    } ?>
                </td>
                <td>用户名：</td>
                <td><?= $userModel->user_name ?></td>
                <td>身份证号码：</td>
                <td>
                    <?php if (is_object($data)) {
                        echo $data->card_number;
                    } else {
                        echo '--';
                    } ?>
                </td>
            </tr>
            <tr>
                <td>资产总额：</td>
                <td><?= Yii::$app->formatter->asCurrency($balance + $freeze)?></td>
                <td>可用余额：</td>
                <td><?= Yii::$app->formatter->asCurrency($balance) ?></td>
                <td>冻结金额：</td>
                <td><?= Yii::$app->formatter->asCurrency($freeze) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
