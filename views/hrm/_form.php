<?php

use app\models\Hrm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Hrm */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$form = ActiveForm::begin([
    'options' => ['data-pjax' => true],
    'action' => Url::current(['hrm/index', 'id' => $model->id, 'state' => ($model->isNewRecord ? 'save' : 'update'),]),
    'fieldConfig' => [
        'template' => '<div class="input-group">{label}{input}</div>{hint}{error}',
        'labelOptions' => [
            'class' => 'input-group-addon',
        ],
    ]
]);
?>

<div class="row">
    <div class="col-sm-3">
        <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'role')->dropDownList(Hrm::$roleList) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <?= $form->field($model, 'gender')->dropDownList(Hrm::$genderList, ['prompt' => '']) ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'nationalCode')->textInput(['class' => 'form-control nationalCodeInputmask']); ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'birthdate')->textInput(['class' => 'form-control hrmBirthdateDatepicker']) ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'fatherName')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?= $form->field($model, 'des')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
        <?= Html::submitButton($model->isNewRecord ? ' <span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Save') : ' <span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['class' => 'btn btn-block btn-social ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>