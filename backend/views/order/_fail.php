<?php

/* @var $id int */

use yii\helpers\Url;

?>
<form class="form-horizontal" action="<?= Url::to(['/order/confirm-fail']) ?>" method="post">
    <input type="hidden" name='id' value="<?= $id ?>">

    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">备注:</label>
            <div class="col-sm-10">
                <textarea placeholder="请输入备注，备注不能为空" class="form-control" name="remark"></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" id="line-down-save" class="btn btn-primary">确定</button>
    </div>
</form>

