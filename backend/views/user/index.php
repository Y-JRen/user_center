<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">


<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'phone',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->phone, ['/user/order', 'uid' => $model->id]);
                }
            ],
            'user_name',
            'email:email',
            //'passwd',
            'status',
            'from_platform',
            'from_channel',
            'reg_time:datetime',
            'reg_ip',
            'login_time:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
