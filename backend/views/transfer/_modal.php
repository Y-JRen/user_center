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
            <td><?=$model->order_id?></td>
            <td>用户</td>
            <td><?=$phone?></td>
        </tr>
        <tr>
            <td>提现金额</td>
            <td><?=$model->amount?></td>
            <td>提现方式</td>
            <td>银行卡</td>
        </tr>
        </tbody>
    </table>
    <div class="callout callout-info lead">
        <h4>银行账号信息</h4>
        <?=$info?>
    </div>
</div>
<form class="form-horizontal" action="/transfer/confirm-success" method="post">
    <div class="box-body">
        <input type="hidden" class="form-control" value="<?=$model->id?>" name='id'>
        <div class="form-group">
            <label class="col-sm-2 control-label">公司部门:</label>
            <div class="col-sm-10">
                <select class="form-control" onchange="" name="organizationId" >
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">收入账号:</label>
            <div class="col-sm-10">
                <select class="form-control" onchange="" name="inputAccount" >
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">流水号</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name='back_order'>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
            <button type="submit" id="line-down-save" class="btn btn-primary">确认到账</button>
        </div>
</form>