<?php

use app\models\Type;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin([
    'options' => ['data-pjax' => true],
    'action' => Url::current(['id' => $model->id, 'entityBarcode' => $parentModel->barcode, 'state' => ($model->isNewRecord ? 'save' : 'update'),]),
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
        <?= $form->field($model, 'rawId')->widget(Select2::class, Type::getSelect2FieldConfigRaw($model)); ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'des')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
        <?= Html::submitButton($model->isNewRecord ? ' <span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Save') : ' <span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['class' => 'btn btn-block btn-social ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>