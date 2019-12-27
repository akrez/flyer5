<?php

use app\assets\StretchyAsset;
use app\components\TableView;
use app\models\RawType;
use app\models\Type;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = $newModel::modelTitle($categoryId);
StretchyAsset::register($this);
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
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'name')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'shortname',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'shortname')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'parentId',
                'filter' => true,
                'value' => function ($model, $key, $index, $grid, $form) {
                    $config = [
                        'data' => ($model->parentId && $model->parent ? [$model->parent->id => $model->parent->name] : []),
                        'options' => [
                            'placeholder' => $model->getAttributeLabel('parentId'),
                            'id' => Html::getInputId($model, 'parentId') . '-' . $model->id,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => Url::toRoute(['typefarvand/suggest']),
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ]
                        ],
                    ];
                    return $form->field($model, 'parentId')->widget(Select2::classname(), $config);
                },
                'footer' => true,
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_PART]),
            ],
            [
                'attribute' => 'unit',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'unit')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_RESELLER]),
            ],
            [
                'attribute' => 'des',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'des')->textInput(['maxlength' => true]);
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
                'filter' => false,
                'value' => function ($model, $key, $index, $grid, $form) {
                    return Html::a(' <span class="glyphicon glyphicon-list-alt"></span> ' . RawType::modelName(), Url::toRoute(['/rawtype/index', 'typeId' => $model->id]), ['class' => 'btn btn-default btn-block btn-social']);
                },
                'footer' => false,
            ],
        ],
    ]);
    ?>
</div>
