<?php

use app\models\TypeRaw;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\MaskedInputAsset;
use yii\widgets\Pjax;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use yii\grid\GridViewAsset;
use yii\helpers\HtmlPurifier;
use app\assets\DatepickerAsset;
use app\models\TypePart;
use app\models\TypeReseller;
use kartik\select2\Select2;

$this->title = $newModel::modelName();
MaskedInputAsset::register($this);
DatepickerAsset::register($this);
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

GridViewAsset::register($this);

$sort = $dataProvider->sort;

$this->registerCss("
.table th {
    vertical-align: top !important;
}
.table td {
    vertical-align: middle !important;
}
");

$this->registerJs("
function applyFilter() { 
    $('#table').yiiGridView(" . json_encode([
    'filterUrl' => Url::current(),
    'filterSelector' => '#table-filters input, #table-filters select',
    'filterOnFocusOut' => true,
]) . ");
}
$(document).on('click','.btn[toggle]',function() {

    var btn = $(this);
    var isHidden = $(btn.attr('toggle')).is(':hidden');    

    $('.btn[toggle]').each(function(i) {
        var toggleBtn = $(this);
        $(toggleBtn.attr('toggle')).hide();
        toggleBtn.addClass('btn-default');
        toggleBtn.removeClass('btn-warning');
    });

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
    applyFilter();
});
applyFilter();
", View::POS_READY);
?>

<h3 class="pb20"><?= Html::encode($this->title) ?></h3>

<?php
Pjax::begin([
    'id' => "hrm-pjax",
    'timeout' => false,
    'enablePushState' => false,
]);

$this->registerJs("
$('#table').yiiGridView(" . json_encode([
    'filterUrl' => Url::current(['CategorySearch' => null,]),
    'filterSelector' => '#table-filters input, #table-filters select',
    'filterOnFocusOut' => true,
]) . ");
");

$attributes = ['name', 'shortname'];
if (in_array($newModel::getCategoryClass(), [
    TypeReseller::getCategoryClass(),
    TypeRaw::getCategoryClass(),
])) {
    $attributes[] = 'unit';
}
if ($newModel::getCategoryClass() == TypePart::getCategoryClass()) {
    $attributes[] = 'parentId';
}
$attributes[] = 'des';

$colspan = count($attributes) + 1;
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
            <table id="table" class="table table-bordered table-striped">
                <thead>
                    <tr class="info">
                        <?php
                        if (in_array('id', $attributes)) {
                            echo '<th>' . $sort->link('id', ['label' => $newModel->getAttributeLabel('id')]) . '</th>';
                        }
                        if (in_array('name', $attributes)) {
                            echo '<th>' . $sort->link('name', ['label' => $newModel->getAttributeLabel('name')]) . '</th>';
                        }
                        if (in_array('shortname', $attributes)) {
                            echo '<th>' . $sort->link('shortname', ['label' => $newModel->getAttributeLabel('shortname')]) . '</th>';
                        }
                        if (in_array('unit', $attributes)) {
                            echo '<th>' . $sort->link('unit', ['label' => $newModel->getAttributeLabel('unit')]) . '</th>';
                        }
                        if (in_array('parentId', $attributes)) {
                            echo '<th>' . $sort->link('parentId', ['label' => $newModel->getAttributeLabel('parentId')]) . '</th>';
                        }
                        if (in_array('des', $attributes)) {
                            echo '<th>' . $sort->link('des', ['label' => $newModel->getAttributeLabel('des')]) . '</th>';
                        }
                        ?>
                        <th></th>
                    </tr>
                    <tr id="table-filters" class="info">
                        <?php
                        if (in_array('id', $attributes)) {
                            echo '<th>' . Html::activeInput('id', $searchModel, 'id', ['class' => 'form-control']) . '</th>';
                        }
                        if (in_array('name', $attributes)) {
                            echo '<th>' . Html::activeInput('name', $searchModel, 'name', ['class' => 'form-control']) . '</th>';
                        }
                        if (in_array('shortname', $attributes)) {
                            echo '<th>' . Html::activeInput('shortname', $searchModel, 'shortname', ['class' => 'form-control']) . '</th>';
                        }
                        if (in_array('unit', $attributes)) {
                            echo '<th>' . Html::activeInput('unit', $searchModel, 'unit', ['class' => 'form-control']) . '</th>';
                        }
                        if (in_array('parentId', $attributes)) {
                            echo '<th>' . Select2::widget($searchModel::getSelect2FieldConfigParent($searchModel)) . '</th>';
                        }
                        if (in_array('des', $attributes)) {
                            echo '<th>' . Html::activeInput('des', $searchModel, 'des', ['class' => 'form-control']) . '</th>';
                        }
                        ?>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataProvider->getModels()) { ?>
                        <?php
                        foreach ($dataProvider->getModels() as $dataProviderModelKey => $dataProviderModel) :
                            $displayState = '';
                            if ($model && $model->id == $dataProviderModel->id) {
                                $displayState = $state;
                                $dataProviderModel = $model;
                            }
                        ?>
                            <tr class="active">
                                <?php
                                if (in_array('id', $attributes)) {
                                    echo '<td>' . HtmlPurifier::process($dataProviderModel->id) . '</td>';
                                }
                                if (in_array('name', $attributes)) {
                                    echo '<td>' . HtmlPurifier::process($dataProviderModel->name) . '</td>';
                                }
                                if (in_array('shortname', $attributes)) {
                                    echo '<td>' . HtmlPurifier::process($dataProviderModel->shortname) . '</td>';
                                }
                                if (in_array('unit', $attributes)) {
                                    echo '<td>' . HtmlPurifier::process($dataProviderModel->unit) . '</td>';
                                }
                                if (in_array('parentId', $attributes)) {
                                    echo '<td>' . HtmlPurifier::process($dataProviderModel->parent->printNameAndShortname()) . '</td>';
                                }
                                if (in_array('des', $attributes)) {
                                    echo '<td>' . HtmlPurifier::process($dataProviderModel->des) . '</td>';
                                }
                                ?>
                                <td>
                                    <?= Html::button(Yii::t('app', 'Update'), ['class' => 'btn btn-block' . ($displayState == 'update' ? ' btn-warning ' : ' btn-default '), 'toggle' => "#row-update-" . $dataProviderModel->id]) ?>
                                </td>
                            </tr>
                            <?php
                            $displayStyle = 'display: none;';
                            if ($model && $model->id == $dataProviderModel->id) {
                                $dataProviderModel = $model;
                                $displayStyle = 'display: table-row;';
                            }
                            ?>
                            <tr style="<?= $displayStyle ?>" id="<?= "row-update-" . $dataProviderModel->id ?>">
                                <td colspan="<?= $colspan ?>">
                                    <?= $this->render('_form', ['model' => $dataProviderModel]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php } else { ?>
                        <tr class="danger">
                            <td colspan="<?= $colspan ?>">
                                <?= Yii::t('yii', 'No results found.') ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr class="success">
                        <td colspan="<?= $colspan ?>">
                            <?= $this->render('_form', ['model' => $newModel]) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
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
?>
<?php Pjax::end(); ?>