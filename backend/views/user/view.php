<?php

use common\models\UserInfo;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->phone;
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">基本登录信息</h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'phone',
                'user_name',
                'email:email',
                [
                    'attribute' => 'status',
                    'value' => $model->status == 1 ? '正常' : '禁用',
                ],
                [
                    'attribute' => 'from_platform',
                    'value' => ArrayHelper::getValue(Config::getPlatformArray(), $model->from_platform),
                ],
                'from_channel',
                'reg_time:datetime',
                'reg_ip',
                'login_time:datetime',
            ],
        ]) ?>
    </div>
    <?php if ($model->userInfo): ?>
        <div class="box-header with-border">
            <h3 class="box-title">用户扩展信息</h3>
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model->userInfo,
                'attributes' => [
                    'real_name',
                    'card_number',
                    'birthday',
                    [
                        'attribute' => 'sex',
                        'value' => ArrayHelper::getValue(UserInfo::$sexArr, $model->userInfo->sex),
                    ],
                    [
                        'attribute' => 'is_real',
                        'value' => ArrayHelper::getValue(UserInfo::$realArr, $model->userInfo->is_real),
                    ],
                    'area',
                    'city',
                    'county',
                    [
                        'attribute' => 'funds_status',
                        'value' => ArrayHelper::getValue(UserInfo::$fundsArr, $model->userInfo->funds_status),
                    ],
                ],
            ]) ?>
        </div>
    <?php endif; ?>
</div>

