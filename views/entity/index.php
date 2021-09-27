<?php

use app\assets\DatepickerAsset;
use app\components\TableView;
use app\models\Entity;
use app\models\RawEntity;
use app\models\Relation;
use app\models\Type;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\LinkPager;

$this->title = Entity::modelTitle($categoryId);
DatepickerAsset::register($this);
$this->registerJs('
    $(".entitySubmitatDatepicker, .entityFactoratDatepicker, .entityProductatDatepicker").persianDatepicker({
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
            [
                'attribute' => '_count',
                'filter' => false,
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $index + 1;
                },
                'footer' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, '_count')->textInput(['typ' => 'number']);
                },
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_RESELLER]),
            ],
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'id')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'typeId',
                'filter' => true,
                'value' => function ($model, $key, $index, $grid, $form) {
                    $url = null;
                    if ($model->categoryId == Type::CATEGORY_SAMANE) {
                        $url = 'typesamane/suggest';
                    }
                    if ($model->categoryId == Type::CATEGORY_FARVAND) {
                        $url = 'typefarvand/suggest';
                    }
                    if ($model->categoryId == Type::CATEGORY_PART) {
                        $url = 'typepart/suggest';
                    }
                    if ($model->categoryId == Type::CATEGORY_RESELLER) {
                        $url = 'typereseller/suggest';
                    }
                    $config = [
                        'data' => ($model->typeId && $model->type ? [$model->type->id => $model->type->name . ' (' . $model->type->shortname . ')'] : []),
                        'options' => [
                            'placeholder' => $model->getAttributeLabel('typeId'),
                            'id' => Html::getInputId($model, 'typeId') . '-' . ($model->isNewRecord ? 'new-' : 'old-') . $model->id,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => Url::toRoute([$url]),
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ]
                        ],
                    ];
                    return $form->field($model, 'typeId')->widget(Select2::classname(), $config);
                },
                'footer' => true,
                'format' => 'raw',
            ],
            [
                'attribute' => 'price',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'price')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_RESELLER]),
            ],
            [
                'attribute' => 'qa',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'qa')->checkbox(['value' => "1"], false);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'qa')->dropDownList([
                                null => '',
                                0 => 'ندارد',
                                1 => 'دارد',
                    ]);
                },
                'visible' => in_array($categoryId, [Type::CATEGORY_FARVAND, Type::CATEGORY_PART]),
            ],
            [
                'attribute' => 'qc',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'qc')->checkbox(['value' => "1"], false);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'qc')->dropDownList([
                                null => '',
                                0 => 'ندارد',
                                1 => 'دارد',
                    ]);
                },
                'visible' => in_array($categoryId, [Type::CATEGORY_FARVAND, Type::CATEGORY_PART]),
            ],
            [
                'attribute' => 'factor',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'factor')->textInput(['maxlength' => true]);
                },
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_RESELLER]),
            ],
            [
                'attribute' => 'factorAt',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'factorAt')->textInput(['class' => 'form-control entityFactoratDatepicker']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'factorAt')->textInput();
                },
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_RESELLER]),
            ],
            [
                'attribute' => 'submitAt',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'submitAt')->textInput(['class' => 'form-control entitySubmitatDatepicker']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'submitAt')->textInput();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'productAt',
                'value' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'productAt')->textInput(['class' => 'form-control entityProductatDatepicker']);
                },
                'filter' => function ($model, $key, $index, $grid, $form) {
                    return $form->field($model, 'productAt')->textInput();
                },
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_FARVAND, Type::CATEGORY_PART]),
            ],
            [
                'attribute' => 'providerId',
                'filter' => true,
                'value' => function ($model, $key, $index, $grid, $form) {
                    $config = [
                        'data' => ($model->providerId && $model->provider ? [$model->provider->id => $model->provider->fullname . ' (' . $model->provider->code . ')'] : []),
                        'options' => [
                            'placeholder' => $model->getAttributeLabel('providerId'),
                            'id' => Html::getInputId($model, 'providerId') . '-' . ($model->isNewRecord ? 'new-' : 'old-') . $model->id,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => Url::toRoute(['hrm/suggest']),
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ]
                        ],
                    ];
                    return $form->field($model, 'providerId')->widget(Select2::classname(), $config);
                },
                'footer' => true,
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_FARVAND, Type::CATEGORY_PART, Type::CATEGORY_RESELLER,]),
            ],
            [
                'attribute' => 'sellerId',
                'filter' => true,
                'value' => function ($model, $key, $index, $grid, $form) {
                    $config = [
                        'data' => ($model->sellerId && $model->seller ? [$model->seller->id => $model->seller->fullname . ' (' . $model->seller->code . ')'] : []),
                        'options' => [
                            'placeholder' => $model->getAttributeLabel('sellerId'),
                            'id' => Html::getInputId($model, 'sellerId') . '-' . ($model->isNewRecord ? 'new-' : 'old-') . $model->id,
                            'dir' => 'rtl',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'ajax' => [
                                'url' => Url::toRoute(['hrm/suggest']),
                                'dataType' => 'json',
                                'delay' => 250,
                                'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ]
                        ],
                    ];
                    return $form->field($model, 'sellerId')->widget(Select2::classname(), $config);
                },
                'footer' => true,
                'format' => 'raw',
                'visible' => in_array($categoryId, [Type::CATEGORY_RESELLER]),
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
            [
                'label' => '',
                'format' => 'raw',
                'filter' => false,
                'value' => function ($model, $key, $index, $grid, $form) {
                    return Html::a(' <span class="glyphicon glyphicon-list-alt"></span> ' . Relation::modelName(), Url::toRoute(['/relation/index', 'parentId' => $model->id]), ['class' => 'btn btn-default btn-block btn-social']);
                },
                'footer' => false,
            ],
            [
                'label' => '',
                'format' => 'raw',
                'filter' => false,
                'value' => function ($model, $key, $index, $grid, $form) {
                    return Html::a(' <span class="glyphicon glyphicon-oil"></span> ' . RawEntity::modelName(), Url::toRoute(['/rawentity/index', 'entityId' => $model->id]), ['class' => 'btn btn-default btn-block btn-social']);
                },
                'footer' => false,
            ],
        ],
    ]);
    ?>
</div>

<?=
LinkPager::widget([
    'pagination' => $dataProvider->getPagination(),
]);
?>