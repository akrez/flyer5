<?php

namespace app\components;

use Closure;
use yii\base\BaseObject;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\base\Model;

class TableDataColumn extends BaseObject
{

    // Just for comatibility
    public $header = null;
    public $content = null;
    public $grid = null;
    public $filterInputOptions = [];
    public $options = [];
    public $attribute = null;
    public $format = 'text';
    public $label = null;
    public $filter = null;
    public $value = null;
    public $footer = null;
    public $encodeLabel = true;
    public $enableSorting = true;
    public $sortLinkOptions = [];
    public $visible = true;
    public $headerOptions = [];
    public $filterOptions = [];
    public $contentOptions = [];
    public $footerOptions = [];

    public function renderHeaderCell()
    {
        if ($this->label === null && $this->attribute === null) {
            return $this->grid->emptyCell;
        }

        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        $sort = $this->grid->sort;
        if ($this->attribute !== null && $this->enableSorting && $sort !== false && $sort->hasAttribute($this->attribute)) {
            return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label]));
        }

        return $label;
    }

    protected function getHeaderCellLabel()
    {
        if ($this->label === null) {
            if ($this->grid->modelClass !== null && $this->grid->modelClass instanceof Model) {
                $model = $this->grid->modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($this->grid->filterModel !== null && $this->grid->filterModel instanceof Model) {
                $label = $this->grid->filterModel->getAttributeLabel($this->attribute);
            } else {
                if (($model = reset($this->grid->models)) instanceof Model) {
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

    public function renderFilterCell()
    {
        $model = $this->grid->filterModel;
        $form = $this->grid->form;
        if ($this->filter === false) {
        } elseif ($this->filter instanceof Closure) {
            return call_user_func($this->filter, $model, null, null, $this, $form);
        } elseif ($this->value !== false && $this->value instanceof Closure) {
            return call_user_func($this->value, $model, null, null, $this, $form);
        } elseif ($this->footer !== false && $this->footer instanceof Closure) {
            return call_user_func($this->footer, $model, null, null, $this, $form);
        }
        return null;
    }

    public function renderDataCell($model, $key, $index)
    {
        $form = $this->grid->form;
        return $this->grid->formatter->format($this->getDataCellValue($model, $key, $index, $form), $this->format);
    }

    public function renderFooterCell()
    {
        $model = $this->grid->footerModel;
        $form = $this->grid->form;
        if ($this->footer instanceof Closure) {
            return call_user_func($this->footer, $model, null, null, $this, $form);
        } elseif ($this->footer !== false && $this->value instanceof Closure) {
            return call_user_func($this->value, $model, null, null, $this, $form);
        }
        return null;
    }

    public function getDataCellValue($model, $key, $index, $form)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            } elseif ($this->value instanceof Closure) {
                return call_user_func($this->value, $model, $key, $index, $this, $form);
            }
        } elseif ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }
        return null;
    }
}
