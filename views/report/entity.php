<?php

use app\models\Entity;
use yii\grid\GridView;
use app\models\EntitySearch;

echo GridView::widget([
    'layout' => ' <div class="panel-heading">' . Entity::modelTitle() . ' </div> {items} ',
    'options' => ['class' => 'panel panel-primary'],
    'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
    'summary' => false,
    'filterModel' => $entitySearch,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'dataProvider' => $entitySearchProvider,
    'columns' => Entity::getGridViewColumns([], new EntitySearch(), new Entity()),
]);
