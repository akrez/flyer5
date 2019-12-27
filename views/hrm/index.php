<?php

use app\assets\DatepickerAsset;
use app\assets\StretchyAsset;
use app\components\TableView;
use app\models\Hrm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\MaskedInputAsset;

$this->title = $newModel::modelName();
MaskedInputAsset::register($this);
DatepickerAsset::register($this);
StretchyAsset::register($this);
$this->registerJs('
    jQuery(".nationalCodeInputmask").inputmask({"mask":"999-999999-9"});
    jQuery(".mobileInputmask").inputmask({"mask":"9999-9999999"});
    $(".hrmBirthdateDatepicker").persianDatepicker({
        calendar: {
            persian: {
                showHint: true,
                locale: "fa"
            },
            gregorian: {
                showHint: true
            }
        },
        "toolbox": {
            "calendarSwitch": {
                "enabled": false,
            }
        },
        initialValue: false,
        initialValueType: "persian",
        autoClose: true,
        observer: true,
        format: "YYYY-MM-DD"
    });
', View::POS_END);
?>

<h1 class="pb20"><?= Html::encode($this->title) ?></h1>

<div class="table-responsive">

    <?=
    TableView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'footerModel' => $newModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                'action' => Url::current(['id' => $model->id]),
            ];
        },
        'filterRowOptions' => [
            'action' => Url::current(),
            'method' => 'get',
        ],
        'footerRowOptions' => [
            'action' => Url::current(['id' => null]),
        ],
        'columns' => [
            'id',
            [
                'attribute' => 'code',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'code')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'role',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'role')->dropDownList(Hrm::$roleList);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'role')->dropDownList(Hrm::$roleList, ['prompt' => '']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'fullname',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'fullname')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'fatherName',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'fatherName')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'gender',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'gender')->dropDownList(Hrm::$genderList, ['prompt' => '']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'nationalCode',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'nationalCode')->textInput(['class' => 'form-control nationalCodeInputmask']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'nationalCode')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'birthdate',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'birthdate')->textInput(['class' => 'form-control hrmBirthdateDatepicker']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'birthdate')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'mobile',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'mobile')->textInput(['class' => 'form-control mobileInputmask']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'mobile')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'des',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'des')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
            ],
            [
                'label' => '',
                'format' => 'raw',
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return Html::submitButton(' <span class="glyphicon glyphicon-search"></span> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-info btn-block btn-social']);
                },
                'value' => function ($model, $key, $index, $grid, $form) {
                    return Html::submitButton(' <span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['class' => 'btn btn-primary btn-block btn-social']);
                },
                'footer' => function ($model, $key, $index, $grid, $form) {
                    return Html::submitButton(' <span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block btn-social']);
                },
            ],
        ],
    ]);
    ?>
</div>
