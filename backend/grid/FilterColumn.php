<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/7/24
 * Time: 18:59
 */

namespace backend\grid;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class FilterColumn extends Column
{
    public $attribute;

    public $label;

    public $value;

    public $format = 'text';

    public $filterArray;

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        $label = $this->getHeaderCellLabel();

        $params = \Yii::$app->request->get($this->attribute, []);

        $class = empty($params) ? '' : 'c-blue';
        $html = '<div class="lte-filterbox">';
        $html .= "<i title=\"Filter Menu\" class=\"fa fa-filter lte-dropdown-trigger ml-0 {$class}\"></i>";
        $html .= '<div class="lte-table-filter-dropdown none">';
        $html .= '<ul class="lte-dropdown-menu lte-dropdown-menu-vertical  lte-dropdown-menu-root" role="menu" aria-activedescendant="" tabindex="0">';
        foreach ($this->filterArray as $k => $v) {
            $checked = in_array($k, $params) ? 'checked' : '';
            $html .= ' <li class="lte-dropdown-menu-item" role="menuitem" aria-selected="false">';
            $html .= '<label class="lte-checkbox-wrapper">';
            $html .= "<input type=\"checkbox\" class=\"lte-checkbox-input[]\"  name=\"{$this->attribute}[]\" value=\"{$k}\" {$checked}>";
            $html .= '<span>' . $v . '</span>';
            $html .= '</label>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '<div class="lte-table-filter-dropdown-btns">';
        $html .= '<a class="lte-table-filter-dropdown-link confirm sub">确定</a>';
        $html .= '<a class="lte-table-filter-dropdown-link clean">重置</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $label . $html;
    }

    /**
     * @inheritdoc
     * @since 2.0.8
     */
    protected function getHeaderCellLabel()
    {
        $provider = $this->grid->dataProvider;

        if ($this->label === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                /* @var $model Model */
                $model = new $provider->query->modelClass;
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($provider instanceof ArrayDataProvider && $provider->modelClass !== null) {
                /* @var $model Model */
                $model = new $provider->modelClass;
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($this->grid->filterModel !== null && $this->grid->filterModel instanceof Model) {
                $label = $this->grid->filterModel->getAttributeLabel($this->attribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model Model */
                    $label = $model->getAttributeLabel($this->attribute);
                } else {
                    $label = Inflector::camel2words($this->attribute);
                }
            }
        } else {
            $label = $this->label;
        }

        return $label;
    }

    /**
     * Returns the data cell value.
     * @param mixed $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @return string the data cell value
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            } else {
                return call_user_func($this->value, $model, $key, $index, $this);
            }
        } elseif ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            return $this->grid->formatter->format($this->getDataCellValue($model, $key, $index), $this->format);
        } else {
            return parent::renderDataCellContent($model, $key, $index);
        }
    }
}