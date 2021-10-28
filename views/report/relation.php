<?php

use app\models\Entity;
use app\models\EntitySearch;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$columns = [];
for ($i = 1; $i <= max($modelsLevel); $i++) {
    $columns[] = [
        'attribute' => 'barcode',
        'value' => function ($model) use ($i, $modelsLevel) {
            if (!isset($modelsLevel[$model->barcode]) || $modelsLevel[$model->barcode] == $i) {
                return $model->barcode;
            }
        }
    ];
}
$columns = array_merge($columns, Entity::getGridViewColumns([
    'barcode' => false,
    'qty' => false,
], new EntitySearch(), new Entity()));

echo GridView::widget([
    'layout' => ' <div class="panel-heading">' . $model->barcode . ' (' . $model->type->name . ')' . ' </div> {items} ',
    'options' => ['class' => 'panel panel-primary'],
    'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
    'summary' => false,
    'filterModel' => null,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'dataProvider' => new ArrayDataProvider(['allModels' => $models, 'pagination' => false, 'sort' => false,]),
    'columns' => $columns,
]);
