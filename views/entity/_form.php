<?php

use app\models\Hrm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Entity */
/* @var $form ActiveForm */


$form = ActiveForm::begin([
    'options' => ['data-pjax' => true],
    'action' => Url::current(['barcode' => $model->barcode, 'state' => ($model->isNewRecord ? 'save' : 'update'),]),
    'fieldConfig' => [
        'template' => '<div class="input-group">{label}{input}</div>{hint}{error}',
        'labelOptions' => [
            'class' => 'input-group-addon',
        ],
    ],
]);
if (empty(mb_strlen($model->barcode))) {
    $model->barcode = $model::suggestBarcode();
}
?>

<div class="row">
    <?php if ($model->isNewRecord) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'count') ?>
        </div>
        <?php if ($visableAttributes['barcode']) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'barcode') ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<div class="row">
    <?php if ($visableAttributes['typeId']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'typeId')->widget(Select2::class, $model::getSelect2FieldConfigType($model)) ?>
        </div>
    <?php } ?>
    <?php if ($visableAttributes['qty']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'qty')->textInput(['class' => 'form-control']) ?>
        </div>
    <?php } ?>
</div>

<div class="row">
    <?php if ($visableAttributes['providerId']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'providerId')->widget(Select2::class, Hrm::getSelect2FieldConfigProvider($model)) ?>
        </div>
    <?php } ?>
    <?php if ($visableAttributes['productAt']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'productAt')->textInput(['class' => 'form-control entityProductatDatepicker']) ?>
        </div>
    <?php } ?>
    <?php if ($visableAttributes['qc']) { ?>
        <div class="col-sm-1">
            <?= $form->field($model, 'qc', ['template' => "{label}\n{input}\n{hint}\n{error}", 'labelOptions' => ['class' => 'control-label'],])->checkbox(['value' => "1"], false) ?>
        </div>
    <?php } ?>
    <?php if ($visableAttributes['qa']) { ?>
        <div class="col-sm-1">
            <?= $form->field($model, 'qa', ['template' => "{label}\n{input}\n{hint}\n{error}", 'labelOptions' => ['class' => 'control-label'],])->checkbox(['value' => "1"], false) ?>
        </div>
    <?php } ?>
</div>

<div class="row">
    <?php if ($visableAttributes['sellerId']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'sellerId')->widget(Select2::class, Hrm::getSelect2FieldConfigSeller($model)) ?>
        </div>
    <?php } ?>
    <?php if ($visableAttributes['factorAt']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'factorAt')->textInput(['class' => 'form-control entityFactoratDatepicker']) ?>
        </div>
    <?php } ?>
    <?php if ($visableAttributes['factor']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'factor') ?>
        </div>
    <?php } ?>
    <?php if ($visableAttributes['price']) { ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'price') ?>
        </div>
    <?php } ?>
</div>

<div class="row">
    <div class="col-sm-3">
        <?= $form->field($model, 'place') ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'submitAt')->textInput(['class' => 'form-control entitySubmitatDatepicker']) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'des') ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
        <?= Html::submitButton($model->isNewRecord ? ' <span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Save') : ' <span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['class' => 'btn btn-block btn-social ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>