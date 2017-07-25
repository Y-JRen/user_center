<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/25
 * Time: 13:41
 */

namespace backend\grid;


use yii\helpers\Html;

class DataColumn extends \yii\grid\DataColumn
{
    public $enableSorting = false;

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || $this->label === null && $this->attribute === null) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        $icon = '';
        if ($this->attribute !== null && $this->enableSorting &&
            ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)
        ) {
            $direction = $sort->getAttributeOrder($this->attribute);
            $upClass = ($direction == SORT_ASC) ? 'on' : '';
            $downClass = ($direction == SORT_DESC) ? 'on' : '';

            $url = $sort->createUrl($this->attribute);

            $html = '<div class="lte-table-column-sorter">';
            $html .= "<span class=\"lte-table-column-sorter-up off {$upClass}\" title=\"↑\" >";
            $html .= '<i class="fa fa-caret-up"></i>';
            $html .= '</span>';
            $html .= "<span class=\"lte-table-column-sorter-down off {$downClass}\" title=\"↓\">";
            $html .= '<i class="fa fa-caret-down"></i>';
            $html .= '</span>';
            $html .= '</div>';

            $icon = Html::a($html, $url);
        }
        return $label . $icon;
    }
}