<?php

use app\assets\DatepickerAsset;
use app\assets\StretchyAsset;
use app\components\TableView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

$this->title = $newModel::modelName();
DatepickerAsset::register($this);
StretchyAsset::register($this);
$this->registerJs('
    $(".rawimportedSubmitatDatepicker, .rawimportedFactoratDatepicker").persianDatepicker({
        calendar: {
            persian: {
                showHint: true,
                locale: "fa"
            },
            gregorian: {
                showHint: true
            }
        },
        "toolbox": {
            "calendarSwitch": {
                "enabled": false,
            }
        },
        initialValue: false,
        initialValueType: "persian",
        autoClose: true,
        observer: true,
        format: "YYYY-MM-DD"
    });
', View::POS_END);
$this->registerCss('
    input {
        min-width: 100%;
    }
');
?>

<h1 class="pb20"><?= Html::encode($this->title) ?></h1>

<div class="table-responsive">

    <?=
    TableView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'footerModel' => $newModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                'action' => Url::current(['id' => $model->id]),
            ];
        },
        'filterRowOptions' => [
            'action' => Url::current(),
            'method' => 'get',
        ],
        'footerRowOptions' => [
            'action' => Url::current(['id' => null]),
        ],
        'columns' => [
            'id',
            [
                'attribute' => 'rawId',
                'filter' => true,
                'value' => function ($model, $key, $index, $grid, $form) {
                    $config = [
                        'data' => ($model->rawId && $model->raw ? [$model->raw->id => $model->raw->name . ' (' . $model->raw->unit . ')'] : []),
                        'options' => [
                            'placeholder' => $model->getAttributeLabel('rawId'),
                            'id' => Html::getInputId($model, 'rawId') . '-' . $model->id,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => Url::toRoute(['raw/suggest']),
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ]
                        ],
                    ];
                    return $form->field($model, 'rawId')->widget(Select2::classname(), $config);
                },
                'footer' => true,
                'format' => 'raw',
            ],
            [
                'attribute' => 'price',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'price')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'providerId',
                'filter' => true,
                'value' => function ($model, $key, $index, $grid, $form) {
                    $config = [
                        'data' => ($model->providerId && $model->provider ? [$model->provider->id => $model->provider->fullname . ' (' . $model->provider->code . ')'] : []),
                        'options' => [
                            'placeholder' => $model->getAttributeLabel('providerId'),
                            'id' => Html::getInputId($model, 'providerId') . '-' . $model->id,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => Url::toRoute(['hrm/suggest']),
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ]
                        ],
                    ];
                    return $form->field($model, 'providerId')->widget(Select2::classname(), $config);
                },
                'footer' => true,
                'format' => 'raw',
            ],
            [
                'attribute' => 'factor',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'factor')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'qty',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'qty')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'sellerId',
                'filter' => true,
                'value' => function ($model, $key, $index, $grid, $form) {
                    $config = [
                        'data' => ($model->sellerId && $model->provider ? [$model->provider->id => $model->provider->fullname . ' (' . $model->provider->code . ')'] : []),
                        'options' => [
                            'placeholder' => $model->getAttributeLabel('sellerId'),
                            'id' => Html::getInputId($model, 'sellerId') . '-' . $model->id,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => Url::toRoute(['hrm/suggest', 'role' => [2]]),
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ]
                        ],
                    ];
                    return $form->field($model, 'sellerId')->widget(Select2::classname(), $config);
                },
                'footer' => true,
                'format' => 'raw',
            ],
            [
                'attribute' => 'submitAt',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'submitAt')->textInput(['class' => 'form-control rawimportedSubmitatDatepicker']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'submitAt')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'factorAt',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'factorAt')->textInput(['class' => 'form-control rawimportedFactoratDatepicker']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'factorAt')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'des',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'des')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'label' => '',
                'format' => 'raw',
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return Html::submitButton(' <span class="glyphicon glyphicon-search"></span> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-info btn-block btn-social']);
                },
                'value' => function ($model, $key, $index, $grid, $form) {
                    return Html::submitButton(' <span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['class' => 'btn btn-primary btn-block btn-social']);
                },
                'footer' => function ($model, $key, $index, $grid, $form) {
                    return Html::submitButton(' <span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block btn-social']);
                },
            ],
        ],
    ]);
    ?>
</div>
