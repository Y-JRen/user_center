<?php
use common\logic\FinanceLogic;

// 获取组织
$organizations = FinanceLogic::instance()->getOrganization();

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">打款确认</h4>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td>提现单号</td>
            <td><?= $model->order_id ?></td>
            <td>用户</td>
            <td><?= $phone ?></td>
        </tr>
        <tr>
            <td>提现金额</td>
            <td><?= $model->amount ?></td>
            <td>提现方式</td>
            <td>银行卡</td>
        </tr>
        </tbody>
    </table>
    <div class="callout callout-info lead">
        <h4>银行账号信息</h4>
        <?= $info ?>
    </div>
</div>
<form class="form-horizontal" action="<?= \yii\helpers\Url::to(['/transfer/confirm-success']) ?>" method="post">
    <div class="box-body">
        <input type="hidden" class="form-control" value="<?= $model->id ?>" name='id'>
        <div class="form-group">
            <input type="hidden" name="org" id="org" value="<?=$organizations[0]['name']?>">
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
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
            <button type="submit" id="line-down-save" class="btn btn-primary">确认打款</button>
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
    createFinType(1,1);
    $('#transaction_time').datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose:true,//自动关闭
        minView:2//最精准的时间选择为日期0-分 1-时 2-日 3-月
    });
</script>
