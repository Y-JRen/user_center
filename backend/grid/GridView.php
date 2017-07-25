<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/25
 * Time: 14:20
 */

namespace backend\grid;


class GridView extends \yii\grid\GridView
{
    public $layout = "{summary}\n{items}\n<div class='row text-right'><div class='col-xs-12'>{pager}</div></div>";

    public $tableOptions = ['class' => 'table table-bordered table-hover', 'style' => "margin-bottom: 20px;"];

    public $dataColumnClass = 'backend\grid\DataColumn';
}