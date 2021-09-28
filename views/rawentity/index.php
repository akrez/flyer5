<?php

use app\components\TableView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = $newModel::modelName() . ': ' . $parentModel->id . ' (' . $parentModel->type->name . ')';
$autocomplete = [];
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
                    return $form->field($model, 'rawId')->widget(Select2::class, $config);
                },
                'footer' => true,
                'format' => 'raw',
            ],
            [
                'attribute' => 'qty',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'qty')->textInput(['maxlength' => true]);
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
            [
                'label' => '',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return Html::a(' <span class="glyphicon glyphicon-trash"></span> ' . Yii::t('app', 'Remove'), Url::current([0 => 'rawentity/delete', 'id' => $model->id, 'entityId' => $model->entityId]), [
                                'class' => 'btn btn-danger btn-block btn-social',
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    ]);
                },
                'filter' => false,
                'footer' => false,
            ],
        ],
    ]);
    ?>
</div>
