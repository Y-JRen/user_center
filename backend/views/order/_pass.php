<?php

use common\helpers\JsonHelper;
use common\logic\FinanceLogic;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

// 获取组织
$organizations = FinanceLogic::instance()->getOrganization();
$remarkArr = JsonHelper::BankHelper($model->remark);

/* @var $model \backend\models\Order */
?>
<style type="text/css">
    .hint-block {
        margin-top: 8px;
    }
</style>
<form class="form-horizontal" action="<?= Url::to(['/order/confirm-pass']) ?>" method="post" id="mark_form">
    <div class="form-group">
        <label class="col-sm-2 control-label">打款人:</label>
        <div class="col-sm-10 hint-block">
            <?= ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'accountName'), 'value') ?>
            <?= ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'bankName'), 'value') ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">打款金额:</label>
        <div class="col-sm-10 hint-block">
            <?= Yii::$app->formatter->asCurrency($model->amount) ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">流水单号:</label>
        <div class="col-sm-10 hint-block">
            <?= ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'referenceNumber'), 'value') ?>
        </div>
    </div>
    <input type="hidden" name='id' value="<?= $model->id ?>">
    <input type="hidden" name='back_order'
           value="<?= ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'referenceNumber'), 'value') ?>">

    <div class="form-group">
        <input type="hidden" name="org" id="org" value="<?= ArrayHelper::getValue(current($organizations), 'name') ?>">
        <label class="col-sm-2 control-label"><span>*</span>公司部门:</label>
        <div class="col-sm-10">
            <select class="form-control" onchange="createAccounts()" name="org_id" id="org_id">
                <?php
                foreach ($organizations as $key => $value) {
                    $prefix = prefix($value['level']);
                    echo "<option value='{$value['id']}'>{$prefix}{$value['name']}</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="account" id="account">
        <label class="col-sm-2 control-label"><span>*</span>收入账号:</label>
        <div class="col-sm-10">
            <select class="form-control" name="account_id" id="account_id">
            </select>
        </div>
    </div>

    <div class="form-group">
        <input type="hidden" name="type_id" id="type_id"/>
        <input type="hidden" name="type" id="type"/>
        <label class="col-sm-2 control-label"><span>*</span>收入类型:</label>
        <div class="col-sm-10" id="finTypeDiv">

        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label"><span>*</span>到账时间</label>

        <div class="col-sm-10">
            <input type="text" class="form-control" name='transaction_time' id="transaction_time">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">备注</label>

        <div class="col-sm-10">
            <input type="text" class="form-control" name='remark'>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><span>*</span>财务流水</label>

        <div class="col-sm-10 hint-block">
            <label><input type="radio" checked="" value="1" name="sync">自动生成</label>
            <label><input type="radio" name="sync" value="0">不生成</label>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" id="line-down-save" class="btn btn-primary">确认到账</button>
    </div>
</form>
<?php
function prefix($num)
{
    $str = '';
    for ($i = 1; $i < $num; $i++) {
        $str .= '----';
    }
    return $str;
}

?>

<script type="text/javascript">
    createAccounts();
    createFinType(1, 1);
    $('#transaction_time').datetimepicker({
        format: 'yyyy-mm-dd',
        pickerPosition: 'top-right',
        autoclose: true,//自动关闭
        minView: 2//最精准的时间选择为日期0-分 1-时 2-日 3-月
    });
</script>
