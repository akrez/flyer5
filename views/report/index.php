<?php

use app\models\Entity;
use app\models\EntityLog;
use app\models\EntitySearch;
use app\models\RawEntity;
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
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary" style="position: relative;">
            <div class="ajax-splash-show splash-style"></div>
            <div class="panel-heading"><?= $model->barcode . ' (' . $model->type->name . ')' ?></div>
            <?= GridView::widget([
                'layout' => ' {items} ',
                'options' => ['class' => 'table-responsive'],
                'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
                'summary' => false,
                'filterModel' => null,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
                'dataProvider' => new ArrayDataProvider(['allModels' => $models, 'pagination' => false, 'sort' => false,]),
                'columns' => $columns,
            ]);
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary" style="position: relative;">
            <div class="ajax-splash-show splash-style"></div>
            <div class="panel-heading"><?= RawEntity::modelTitle() ?></div>
            <?= GridView::widget([
                'layout' => ' {items} ',
                'options' => ['class' => 'table-responsive'],
                'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
                'summary' => false,
                'filterModel' => null,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
                'dataProvider' => $rawEntitySearchDataProvider,
                'columns' => RawEntity::getGridViewColumns([], $rawEntitySearch, new RawEntity()),
            ]);
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary" style="position: relative;">
            <div class="ajax-splash-show splash-style"></div>
            <div class="panel-heading"><?= RawEntity::modelTitle() ?></div>
            <?= GridView::widget([
                'layout' => ' {items} ',
                'options' => ['class' => 'table-responsive'],
                'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
                'summary' => false,
                'filterModel' => null,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
                'dataProvider' => $entityLogSearchDataProvider,
                'columns' => EntityLog::getGridViewColumns([], $entityLogSearch, new EntityLog()),
            ]);
            ?>
        </div>
    </div>
</div>