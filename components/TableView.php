<?php

namespace app\components;

use Closure;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\widgets\ActiveForm;

class TableView extends Widget
{

    // Just for compatibility and fill models,sort,modelClass
    public $dataProvider = null;
    public $form = null;
    public $columnFormat = 'text';
    public $formatter = null;
    public $columns = [];
    public $dataColumnClass;
    public $models = null;
    public $sort = null;
    public $modelClass = null;
    public $filterModel;
    public $footerModel = null;
    public $rowOptions = [];
    public $filterRowOptions = ['class' => 'table-view-row-filter table-view-row'];
    public $footerRowOptions = ['class' => 'table-view-row-footer table-view-row'];
    public $headerRowOptions = ['class' => 'table-view-row-header table-view-row'];
    public $tableOptions = ['class' => 'table-view'];
    public $emptyCell = '&nbsp;';

    public function init()
    {
        parent::init();
        $this->guessByDataProvider();
        $this->initFormatter();
        $this->initColumns();
    }

    public function run()
    {
        $this->Assets();

        echo Html::beginTag('div', $this->tableOptions);

        echo $this->renderTableHeader();
        $this->renderFilters();
        $this->renderTableBody();
        $this->renderTableFooter();

        echo Html::endTag('div');
    }

    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            $cells[] = Html::tag('div', $column->renderHeaderCell(), ['class' => 'table-view-cell']);
        }
        return Html::tag('div', implode('', $cells), $this->headerRowOptions);
    }

    public function renderFilters()
    {
        if ($this->filterModel !== null) {
            if ($this->filterRowOptions instanceof Closure) {
                $options = call_user_func($this->filterRowOptions, $this->filterModel, $key, $index, $this);
            } else {
                $options = $this->filterRowOptions;
            }
            $options['options']['class'] = ArrayHelper::getValue($options, 'options.class', '') . ' table-view-row table-view-row-filter ';
            $options['fieldConfig']['template'] = ArrayHelper::getValue($options, 'fieldConfig.template', "{input}\n{hint}\n{error}");
            $this->form = new ActiveForm($options);
            foreach ($this->columns as $i => $column) {
                echo Html::beginTag('div', ['class' => 'table-view-cell']);
                echo $column->renderFilterCell();
                echo Html::endTag('div');
            }
            echo $this->form->run();
        }
    }

    public function renderTableBody()
    {
        $key = 0;
        if (count($this->models) == 0) {
            echo Html::tag('div', '<p style="width: 100%; margin: 10px;">' . \Yii::t('yii', 'No results found.') . '</p>', ['class' => 'table-view-row', 'style' => 'text-align: center;']);
            return;
        }
        foreach ($this->models as $index => $model) {
            if ($this->rowOptions instanceof Closure) {
                $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
            } else {
                $options = $this->rowOptions;
            }
            $options['options']['class'] = ArrayHelper::getValue($options, 'options.class', '') . ' table-view-row ';
            $options['fieldConfig']['template'] = ArrayHelper::getValue($options, 'fieldConfig.template', "{input}\n{hint}\n{error}");
            $this->form = new ActiveForm($options);
            foreach ($this->columns as $i => $column) {
                echo Html::beginTag('div', ['class' => 'table-view-cell']);
                echo $column->renderDataCell($model, $key, $index);
                echo Html::endTag('div');
            }
            echo $this->form->run();
            $key++;
        }
    }

    public function renderTableFooter()
    {
        if ($this->footerModel !== null) {
            if ($this->footerRowOptions instanceof Closure) {
                $options = call_user_func($this->footerRowOptions, $this->footerModel, null, null, $this);
            } else {
                $options = $this->footerRowOptions;
            }
            $options['options']['class'] = ArrayHelper::getValue($options, 'options.class', '') . ' table-view-row ';
            $options['fieldConfig']['template'] = ArrayHelper::getValue($options, 'fieldConfig.template', "{input}\n{hint}\n{error}");
            $this->form = new ActiveForm($options);
            foreach ($this->columns as $i => $column) {
                echo Html::beginTag('div', ['class' => 'table-view-cell']);
                echo $column->renderFooterCell();
                echo Html::endTag('div');
            }
            echo $this->form->run();
        }
    }

    public function Assets()
    {
        $this->getView()->registerCss('
        .table-view { display: table; border: 1px solid white; border-collapse: collapse; width: 100%; } 
        .table-view .table-view-row { display: table-row; }
        .table-view .table-view-row .table-view-cell { display: table-cell; vertical-align: middle; padding: 4px; border: 1px solid white; text-align: center; }
        .table-view .form-group { margin-bottom: 0px; }
        .table-view .form-control { padding: 4px; }
        .table-view .help-block { margin: 4px 0; }
        .table-view .select2-container--krajee[dir="rtl"] .select2-selection--single { padding-left: 48px; padding-right: 12px; }
        .table-view .table-view-row-header { background-color: #f9f9f9; }
        .table-view .table-view-row-filter {  }
        ');
    }

    protected function initFormatter()
    {
        if ($this->formatter === null) {
            $this->formatter = Yii::$app->getFormatter();
        } elseif (is_array($this->formatter)) {
            $this->formatter = Yii::createObject($this->formatter);
        }
        if (!$this->formatter instanceof Formatter) {
            throw new InvalidConfigException('The "formatter" property must be either a Format object or a configuration array.');
        }
    }

    protected function guessByDataProvider()
    {
        if ($this->models === null && $this->dataProvider instanceof ActiveDataProvider) {
            $this->models = $this->dataProvider->getModels();
        }
        if ($this->sort === null && $this->dataProvider instanceof ActiveDataProvider) {
            $this->sort = $this->dataProvider->getSort();
        }
        if ($this->modelClass === null && $this->dataProvider instanceof ActiveDataProvider) {
            $this->modelClass = $this->dataProvider->query->modelClass;
        }
    }

    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ? $this->dataColumnClass : TableDataColumn::class,
                    'grid' => $this,
                    'format' => $this->columnFormat,
                ], $column));
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }

    /**
     * Creates a [[DataColumn]] object based on a string in the format of "attribute:format:label".
     * @param string $text the column specification string
     * @return DataColumn the column instance
     * @throws InvalidConfigException if the column specification is invalid
     */
    protected function createDataColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
        }

        return Yii::createObject([
            'class' => $this->dataColumnClass ? $this->dataColumnClass : TableDataColumn::class,
            'grid' => $this,
            'attribute' => $matches[1],
            'format' => isset($matches[3]) ? $matches[3] : $this->columnFormat,
            'label' => isset($matches[5]) ? $matches[5] : null,
        ]);
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns()
    {
        $models = $this->dataProvider->getModels();
        $model = reset($models);
        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $this->columns[] = (string) $name;
                }
            }
        }
    }
}
