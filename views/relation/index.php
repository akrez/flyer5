<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = $relation::modelName() . ': ' . $model->id . ' (' . $model->type->name . ')';
?>

<?php Pjax::begin(['enablePushState' => true, 'timeout' => 10000, 'id' => 'p0']) ?>

<?php
$form = ActiveForm::begin([
            'options' => ['data-pjax' => ''],
            'fieldConfig' => [
                'template' => '<div class="input-group">{label}{input}</div>{error}',
                'labelOptions' => ['class' => 'input-group-addon'],
            ]
        ]);
?>

<h1 class="pb20"><?= Html::encode($this->title) ?></h1>

<div class="row">
    <?= $form->field($relation, 'id', ['options' => ['class' => 'col-sm-4']])->textInput() ?>
    <div class="col-sm-2">
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success btn-block']) ?>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <?= Html::a(' چاپ ', ['relation/chart', 'parentId' => $model->id], ['class' => 'btn btn-primary btn-block', 'data-pjax' => '0']) ?>
        </div>
    </div>
</div>

<div class="table-responsive">
    <?php
    $visible = ['RawEntity', 'Relation',];
    echo GridView::widget([
        'layout' => ' <div class="panel-heading">موجودیت والد</div> {items} ',
        'options' => ['class' => 'panel panel-warning'],
        'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
        'summary' => false,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
        'dataProvider' => new ArrayDataProvider(['allModels' => ($parentModel ? [$parentModel] : []), 'pagination' => false, 'sort' => false, 'modelClass' => ($parentModel ? null : 'app\models\Entity' )]),
        'columns' => require ('_columns.php'),
    ]);
    ?>
</div>

<div class="table-responsive">
    <?php
    $visible = ['RawEntity',];
    echo GridView::widget([
        'layout' => ' <div class="panel-heading">' . $model->id . ' (' . $model->type->name . ')' . ' </div> {items} ',
        'options' => ['class' => 'panel panel-primary'],
        'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
        'summary' => false,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
        'dataProvider' => new ArrayDataProvider(['allModels' => [$model], 'pagination' => false, 'sort' => false]),
        'columns' => require ('_columns.php'),
    ]);
    ?>
</div>

<div class="table-responsive">
    <?php
    $visible = ['RawEntity', 'Relation', 'Remove',];
    echo GridView::widget([
        'layout' => ' <div class="panel-heading">' . 'موجودیت‌های به کار رفته در ' . $model->id . ' </div> {items} ',
        'options' => ['class' => 'panel panel-warning'],
        'tableOptions' => ['style' => 'text-align: center;', 'class' => "table table-striped table-bordered"],
        'summary' => false,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
        'dataProvider' => $dataProvider,
        'columns' => require ('_columns.php'),
    ]);
    ?>
</div>

<?php ActiveForm::end(); ?>

<?php Pjax::end() ?>