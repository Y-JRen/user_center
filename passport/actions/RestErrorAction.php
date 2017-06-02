<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/2
 * Time: 11:49
 */

namespace passport\actions;


use yii\web\ErrorAction;

class RestErrorAction extends ErrorAction
{
    public function run()
    {
        return $this->renderAjaxResponse();
    }
}