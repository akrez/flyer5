<?php

use app\models\Hrm;
use app\models\Model;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\Alert;
use yii\grid\GridView;
use app\models\TypeRaw;
use app\models\TypePart;
use app\models\TypeFarvand;
use app\models\TypeReseller;
use app\assets\DatepickerAsset;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use app\models\Relation;
use app\models\RawEntity;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $newModel::modelTitle();
DatepickerAsset::register($this);
?>

<h3 class="pb20"><?= Html::encode($this->title) ?></h3>

<?php
$this->registerJs("
$(document).on('click','.btn[toggle]',function() {
    //
    var btn = $(this);
    var isHidden = $(btn.attr('toggle')).is(':hidden');    
    //
    $('.btn[toggle]').each(function(i) {
        var toggleBtn = $(this);
        $(toggleBtn.attr('toggle')).hide();
        toggleBtn.addClass('btn-default');
        toggleBtn.removeClass('btn-warning');
    });
    //
    if(isHidden) {
        $(btn.attr('toggle')).show();
        btn.addClass('btn-warning');
        btn.removeClass('btn-default');
    }
});

$(document).on('pjax:beforeSend', function(xhr, options) {
    $('.ajax-splash-show').css('display','inline-block');
    $('.ajax-splash-hide').css('display','none');
});
$(document).on('pjax:complete', function(xhr, textStatus, options) {
    $('.ajax-splash-show').css('display','none');
    $('.ajax-splash-hide').css('display','inline-block');
});
");

Pjax::begin([
    'id' => "entity-pjax",
    'timeout' => false,
    'enablePushState' => false,
]);

$this->registerJs("
$('.entitySubmitatDatepicker, .entityFactoratDatepicker, .entityProductatDatepicker').persianDatepicker({
    calendar: {
        persian: {
            showHint: true,
            locale: 'fa'
        },
        gregorian: {
            showHint: true
        }
    },
    'toolbox': {
        'calendarSwitch': {
            'enabled': false,
        }
    },
    initialValue: false,
    initialValueType: 'persian',
    autoClose: true,
    observer: true,
    format: 'YYYY-MM-DD'
});
");

$visableInFarvandAndPart = (in_array($newModel::getCategoryClass(), [TypeFarvand::getCategoryClass(), TypePart::getCategoryClass()]));
$visableInReseller = (in_array($newModel::getCategoryClass(), [TypeReseller::getCategoryClass()]));
$visableInRaw = (in_array($newModel::getCategoryClass(), [TypeRaw::getCategoryClass()]));
$visableAttributes = [
    'qc' => $visableInFarvandAndPart,
    'qa' => $visableInFarvandAndPart,
    'productAt' => $visableInFarvandAndPart,
    'providerId' => $visableInFarvandAndPart || $visableInReseller || $visableInRaw,
    //
    'factor' => $visableInReseller || $visableInRaw,
    'price' => $visableInReseller || $visableInRaw,
    'factorAt' => $visableInReseller || $visableInRaw,
    'sellerId' => $visableInReseller || $visableInRaw,
    //
    'qty' => $visableInRaw,
    //
    'barcode' => true,
    'place' => true,
    'des' => true,
    'submitAt' => true,
    'typeId' => true,
    //
    '_update' => true,
    '_rawEntity' => !$visableInRaw,
];
$colspan = count(array_filter($visableAttributes));
?>
<div class="row">
    <div class="col-sm-12">
        <?= Alert::widget() ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary" style="position: relative;">
            <div class="ajax-splash-show splash-style"></div>
            <div class="panel-heading"><?= Html::encode($this->title) ?></div>
            <?php
            echo GridView::widget([
                'layout' => '{items}',
                'tableOptions' => ['style' => 'margin-bottom: 0px', 'class' => 'table table-bordered table-striped'],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'barcode',
                        'visible' => $visableAttributes['barcode'],
                    ],
                    [
                        'attribute' => 'qty',
                        'visible' => $visableAttributes['qty'],
                    ],
                    [
                        'attribute' => 'qc',
                        'filter' => [
                            0 => Yii::t('yii', 'No'),
                            1 => Yii::t('yii', 'Yes'),
                        ],
                        'visible' => $visableAttributes['qc'],
                        'format' => 'boolean',
                    ],
                    [
                        'attribute' => 'qa',
                        'filter' => [
                            0 => Yii::t('yii', 'No'),
                            1 => Yii::t('yii', 'Yes'),
                        ],
                        'visible' => $visableAttributes['qa'],
                        'format' => 'boolean',
                    ],
                    [
                        'attribute' => 'factor',
                        'visible' => $visableAttributes['factor'],
                    ],
                    [
                        'attribute' => 'price',
                        'visible' => $visableAttributes['price'],
                    ],
                    [
                        'attribute' => 'des',
                        'visible' => $visableAttributes['des'],

                    ],
                    [
                        'attribute' => 'place',
                        'visible' => $visableAttributes['place'],
                    ],
                    [
                        'attribute' => 'submitAt',
                        'filter' => Html::activeInput('text', $searchModel, 'submitAt', ['class' => 'form-control entitySubmitatDatepicker']),
                        'visible' => $visableAttributes['submitAt'],
                    ],
                    [
                        'attribute' => 'factorAt',
                        'filter' => Html::activeInput('text', $searchModel, 'factorAt', ['class' => 'form-control entityFactoratDatepicker']),
                        'visible' => $visableAttributes['factorAt'],
                    ],
                    [
                        'attribute' => 'productAt',
                        'filter' => Html::activeInput('text', $searchModel, 'productAt', ['class' => 'form-control entityProductatDatepicker']),
                        'visible' => $visableAttributes['productAt'],
                    ],
                    [
                        'attribute' => 'providerId',
                        'value' => function ($model) {
                            if ($model->provider) {
                                return $model->provider->printFullnameAndCode();
                            }
                        },
                        'filter' => Select2::widget(Hrm::getSelect2FieldConfigProvider($searchModel)),
                        'visible' => $visableAttributes['providerId'],
                    ],
                    [
                        'attribute' => 'sellerId',
                        'value' => function ($model) {
                            if ($model->seller) {
                                return $model->seller->printFullnameAndCode();
                            }
                        },
                        'filter' => Select2::widget(Hrm::getSelect2FieldConfigSeller($searchModel)),
                        'visible' => $visableAttributes['sellerId'],
                    ],
                    [
                        'attribute' => 'typeId',
                        'value' => function ($model) {
                            if ($model->categoryId == TypeRaw::getCategoryClass()) {
                                return $model->type->printNameAndUnit();
                            }
                            return $model->type->printNameAndShortname();
                        },
                        'filter' => Select2::widget($newModel::getSelect2FieldConfigType($searchModel)),
                        'visible' => $visableAttributes['typeId'],
                    ],
                    [
                        'value' => function ($dataProviderModel) use ($model, $state) {
                            $btnClass = ' btn-default ';
                            if ($model && $model->barcode == $dataProviderModel->barcode && $state == 'update') {
                                $btnClass = ' btn-warning ';
                            }
                            return Html::button(Yii::t('app', 'Update'), ['class' => 'btn btn-block' . $btnClass, 'toggle' => "#row-update-" . $dataProviderModel->barcode]);
                        },
                        'format' => 'raw',
                        'visible' => $visableAttributes['_update'],
                    ],
                    /*
                    [
                        'label' => '',
                        'format' => 'raw',
                        'filter' => false,
                        'value' => function ($model, $key, $index, $grid, $form) {
                            return Html::a(' <span class="glyphicon glyphicon-list-alt"></span> ' . Relation::modelName(), Url::toRoute(['/relation/index', 'parentId' => $model->id]), ['class' => 'btn btn-default btn-block btn-social']);
                        },
                        'footer' => false,
                    ],
                    */
                    [
                        'label' => '',
                        'format' => 'raw',
                        'filter' => false,
                        'value' => function ($model, $key, $index, $grid) {
                            return Html::a(' <span class="glyphicon glyphicon-oil"></span> ' . RawEntity::modelName(), Url::toRoute(['/rawentity/index', 'entityBarcode' => $model->barcode, 'data-pjax' => '']), ['class' => 'btn btn-default btn-block btn-social']);
                        },
                        'visible' => $visableAttributes['_rawEntity'],
                    ],
                ],
                'afterRow' => function ($dataProviderModel) use ($model, $state, $visableAttributes) {
                    $displayStyle = 'display: none;';
                    if ($model && $model->barcode == $dataProviderModel->barcode && $state == 'update') {
                        $dataProviderModel = $model;
                        $displayStyle = 'display: table-row;';
                    }
                    //
                    ob_start();
                    echo $this->render('_form', [
                        'model' => $dataProviderModel,
                        'visableAttributes' => $visableAttributes,
                    ]);
                    $formContent = ob_get_contents();
                    ob_end_clean();
                    //
                    return Html::beginTag('tr', [
                        'style' => $displayStyle,
                        'id' => "row-update-" . $dataProviderModel->barcode,
                    ]) . '<td colspan="16"> ' . $formContent . ' </td></tr>';
                },
            ]);
            ?>
            <div class="panel-footer panel-success" style="padding: 8px;">
                <?= $this->render('_form', [
                    'model' => $newModel,
                    'visableAttributes' => $visableAttributes,
                ]);
                ?>
            </div>
        </div>

    </div>
</div>
<?php

echo LinkPager::widget([
    'pagination' => $dataProvider->getPagination(),
    'options' => [
        'class' => 'pagination',
    ]
]);
Pjax::end();
?>