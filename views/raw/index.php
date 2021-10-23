<?php

use app\components\TableView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $newModel::modelTitle();
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
                'attribute' => 'unit',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'unit')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
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
        ],
    ]);
    ?>
</div>
