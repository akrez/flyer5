<?php

use app\models\TypePart;
use app\models\TypeReseller;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Type */
/* @var $form yii\widgets\ActiveForm */

?>

<?php
$form = ActiveForm::begin([
    'options' => ['data-pjax' => true],
    'action' => Url::current(['id' => $model->id, 'state' => ($model->isNewRecord ? 'save' : 'update'),]),
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
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'shortname')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'des')->textInput(['maxlength' => true]) ?>
    </div>
    <?php if ($model::getCategoryClass() == TypeReseller::getCategoryClass()) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>
        </div>
    <?php } ?>
    <?php if ($model::getCategoryClass() == TypePart::getCategoryClass()) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'parentId')->widget(Select2::classname(), $model::getParentIdSelect2FieldConfig($model)); ?>
        </div>
    <?php } ?>
</div>

<div class="row">
    <div class="col-sm-2">
        <?= Html::submitButton($model->isNewRecord ? ' <span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Save') : ' <span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['class' => 'btn btn-block btn-social ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>