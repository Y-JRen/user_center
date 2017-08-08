<?php
use common\helpers\JsonHelper;
use common\logic\FinanceLogic;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

// 获取组织
$organizations = FinanceLogic::instance()->getOrganization();
$remarkArr = JsonHelper::BankHelper($model->remark);
?>
<form class="form-horizontal" action="<?= Url::to(['/transfer/confirm-success']) ?>" method="post">

    <div class="form-group">
        <label class="col-sm-2 control-label">付款给:</label>
        <div class="col-sm-10 hint-block">
            <p>
                <?= ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'accountName'), 'value') ?>
                <?= ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'bankName'), 'value') ?>
            </p>
            <p>
                <?= ArrayHelper::getValue(ArrayHelper::getValue($remarkArr, 'referenceNumber'), 'value') ?>
            </p>
        </div>
    </div>

    <!--有图片就显示，没有就不显示-->
    <?php $data = ArrayHelper::getValue($remarkArr, 'referenceImg'); if ($data):?>
        <div class="form-group">
            <label class="col-sm-2 control-label">凭证图片:</label>
            <div class="col-sm-10 hint-block">
                <?php foreach ($data['value'] as $image): ?>
                    <?= Html::a('点击查看'."&nbsp;"."&nbsp;", $image, ['target' => '_blank']);?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif;?>

    <input type="hidden" class="form-control" value="<?= $model->id ?>" name='id'>
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
        <label class="col-sm-2 control-label"><span>*</span>打款账号:</label>
        <div class="col-sm-10">
            <select class="form-control" name="account_id" id="account_id">
            </select>
        </div>
    </div>

    <div class="form-group">
        <input type="hidden" name="type_id" id="type_id"/>
        <input type="hidden" name="type" id="type"/>
        <label class="col-sm-2 control-label"><span>*</span>打款类型:</label>
        <div class="col-sm-10" id="finTypeDiv">

        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label"><span>*</span>流水号</label>

        <div class="col-sm-10">
            <input type="text" class="form-control" name='back_order'>
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

        <div class="col-sm-10">
            <input type="radio" checked="" value="1" name="sync">自动生成
            <input type="radio" name="sync" value="0">不生成
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">确认打款</button>
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
    createFinType(2, 1);
    $('#transaction_time').datetimepicker({
        format: 'yyyy-mm-dd',
        pickerPosition: 'top-right',
        autoclose: true,//自动关闭
        minView: 2//最精准的时间选择为日期0-分 1-时 2-日 3-月
    });
</script>
