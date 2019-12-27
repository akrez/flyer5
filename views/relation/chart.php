<?php

use app\assets\TreegridAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'چارت' . ( $parentModel ? ': ' . $parentModel->id : '');
TreegridAsset::register($this);
$this->registerJs('
    $(".tree-grid").treegrid({
        expanderExpandedClass:  "glyphicon glyphicon-minus",
        expanderCollapsedClass: "glyphicon glyphicon-plus"
    }); 
', View::POS_READY);
$this->registerCss('
    .treegrid-indent   {width:32px; height: 16px; display: inline-block; position: relative;}
    .treegrid-expander {width:32px; height: 16px; display: inline-block; position: relative; cursor: pointer;}
    .table > tbody > tr > td {
        vertical-align: middle;
    }
');
?>

<h1 class="pb20"><?= Html::encode($this->title) ?></h1>

<div class="table-responsive">
    <?php
    $visible = [];
    echo GridView::widget([
        'layout' => ' <div class="panel-heading">موجودیت‌ها</div> {items} ',
        'options' => ['class' => 'panel panel-warning'],
        'tableOptions' => ['class' => "table table-striped table-bordered tree-grid"],
        'summary' => false,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
        'dataProvider' => new ArrayDataProvider(['allModels' => $models, 'pagination' => false, 'sort' => false, 'modelClass' => 'app\models\Entity']),
        'rowOptions' => function ($model, $key, $index, $grid) use ($parentMap) {
            return ['class' => 'treegrid-' . $model->id . (isset($parentMap[$model->id]) ? ' treegrid-parent-' . $parentMap[$model->id] : '')];
        },
        'columns' => require ('_columns.php'),
    ]);
    ?>
</div>