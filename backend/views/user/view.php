<?php

use yii\helpers\ArrayHelper;
use backend\models\User;

/* @var $userModel User */
/* @var $carHousekeeper \backend\models\CarHousekeeper */

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
                            if ($data->is_real == 1) {
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
                    <td><?= Yii::$app->formatter->asCurrency($balance + $freeze) ?></td>
                    <td>可用余额：</td>
                    <td><?= Yii::$app->formatter->asCurrency($balance) ?></td>
                    <td>冻结金额：</td>
                    <td><?= Yii::$app->formatter->asCurrency($freeze) ?></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="panel boxshadow-none bdb">
            <div class="panel-heading">
                <p class="pull-right"><a href="#" class="btn btn-primary">新增</a></p>
                <h4><strong>车管家车辆信息</strong></h4>
            </div>
            <?php foreach ($userModel->carHousekeepers as $carHousekeeper): ?>
                <?php if ($carManagement = $carHousekeeper->carManagement): ?>
                    <div class="panel-heading">
                        <p class="pull-right">
                            <a href="#" class="btn btn-primary btn-xs">编辑</a>
                            <a href="#" class="btn btn-danger btn-xs">删除</a>
                        </p>
                        <h5>
                            <strong><?= $carManagement->brand_name, '--', $carManagement->car_brand_type_name ?></strong>
                        </h5>
                    </div>
                    <dl class="clearfix">
                        <div class="col-md-4 mb-md clearfix">
                            <dd style="float:left;width:100px;">终端序列号：</dd>
                            <dd class="col-sm-7"><?= $carHousekeeper->terminal_no ?></dd>
                        </div>
                        <div class="col-md-4 mb-md clearfix">
                            <dd style="float:left;width:100px;">注册时间：</dd>
                            <dd class="col-sm-7"><?= Yii::$app->formatter->asDatetime($carHousekeeper->created_at) ?></dd>
                        </div>
                        <div class="col-md-4 mb-md clearfix">
                            <dd style="float:left;width:100px;">品牌车系车型：</dd>
                            <dd class="col-sm-7">
                                <?= $carManagement->brand_name, '--', $carManagement->car_brand_type_name, '--', $carManagement->car_brand_son_type_name ?>
                            </dd>
                        </div>
                        <div class="col-md-4 mb-md clearfix">
                            <dd style="float:left;width:100px;">车架号：</dd>
                            <dd class="col-sm-7">
                                <?= $carManagement->frame_number ?>
                            </dd>
                        </div>
                        <div class="col-md-4 mb-md clearfix">
                            <dd style="float:left;width:100px;">车牌号：</dd>
                            <dd class="col-sm-7">
                                <?= $carManagement->plate_number ?>
                            </dd>
                        </div>
                        <div class="col-md-4 mb-md clearfix">
                            <dd style="float:left;width:100px;">行驶证照片：</dd>
                            <dd class="col-sm-7">
                                <?php if ($carManagement->driving_license): ?>
                                    <a href="javascript:void(0)"
                                       onclick="imageMore('<?= $carManagement->driving_license ?>')">
                                        点击查看
                                    </a>
                                <?php else: ?>
                                    --
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                    <div class="clear"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

<?php $this->beginBlock('javascript') ?>
    <script type="text/javascript">
        function imageMore(images) {
            var imgArr = images.split(',');
            var arr = [];
            for (i in imgArr) {
                arr.push({"src": imgArr[i]});
            }
            var data = {data: arr};

            layer.photos({
                photos: data,
                anim: 5
            });
        }
    </script>
<?php $this->endBlock() ?>