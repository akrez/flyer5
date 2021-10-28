<?php

use app\models\Entity;
use yii\grid\GridView;
use app\models\EntitySearch;

?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary" style="position: relative;">
            <div class="ajax-splash-show splash-style"></div>
            <div class="panel-heading"><?= Entity::modelTitle() ?></div>
            <?= GridView::widget([
                'layout' => ' {items} ',
                'options' => ['class' => 'table-responsive'],
                'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
                'summary' => false,
                'filterModel' => $entitySearch,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
                'dataProvider' => $entitySearchProvider,
                'options' => ['class' => 'table-responsive'],
                'columns' => Entity::getGridViewColumns([], new EntitySearch(), new Entity()),
            ]);
            ?>
        </div>
    </div>
</div>