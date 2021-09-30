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
use app\models\Hrm;
use app\models\TypePart;
use app\models\TypeReseller;
use kartik\select2\Select2;

$this->title = $newModel::modelName();
DatepickerAsset::register($this);
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

$('.rawimportedSubmitatDatepicker, .rawimportedFactoratDatepicker').persianDatepicker({
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

$colspan = 11;
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
                        echo '<th>' . $sort->link('id', ['label' => $newModel->getAttributeLabel('id')]) . '</th>';
                        echo '<th>' . $sort->link('price', ['label' => $newModel->getAttributeLabel('price')]) . '</th>';
                        echo '<th>' . $sort->link('factor', ['label' => $newModel->getAttributeLabel('factor')]) . '</th>';
                        echo '<th>' . $sort->link('qty', ['label' => $newModel->getAttributeLabel('qty')]) . '</th>';
                        echo '<th>' . $sort->link('sellerId', ['label' => $newModel->getAttributeLabel('sellerId')]) . '</th>';
                        echo '<th>' . $sort->link('submitAt', ['label' => $newModel->getAttributeLabel('submitAt')]) . '</th>';
                        echo '<th>' . $sort->link('factorAt', ['label' => $newModel->getAttributeLabel('factorAt')]) . '</th>';
                        echo '<th>' . $sort->link('des', ['label' => $newModel->getAttributeLabel('des')]) . '</th>';
                        echo '<th>' . $sort->link('providerId', ['label' => $newModel->getAttributeLabel('providerId')]) . '</th>';
                        echo '<th>' . $sort->link('rawId', ['label' => $newModel->getAttributeLabel('rawId')]) . '</th>';
                        ?>
                        <th></th>
                    </tr>
                    <tr id="table-filters" class="info">
                        <?php
                        echo '<th>' . Html::activeInput('id', $searchModel, 'id', ['class' => 'form-control']) . '</th>';
                        echo '<th>' . Html::activeInput('price', $searchModel, 'price', ['class' => 'form-control']) . '</th>';
                        echo '<th>' . Html::activeInput('factor', $searchModel, 'factor', ['class' => 'form-control']) . '</th>';
                        echo '<th>' . Html::activeInput('qty', $searchModel, 'qty', ['class' => 'form-control']) . '</th>';
                        echo '<th>' . Select2::widget(Hrm::getSelect2FieldConfig($searchModel, 'seller', 'sellerId', 2)) . '</th>';
                        echo '<th>' . Html::activeInput('submitAt', $searchModel, 'submitAt', ['class' => 'form-control rawimportedSubmitatDatepicker']) . '</th>';
                        echo '<th>' . Html::activeInput('factorAt', $searchModel, 'factorAt', ['class' => 'form-control rawimportedFactoratDatepicker']) . '</th>';
                        echo '<th>' . Html::activeInput('des', $searchModel, 'des', ['class' => 'form-control']) . '</th>';
                        echo '<th>' . Select2::widget(Hrm::getSelect2FieldConfig($searchModel, 'provider', 'providerId')) . '</th>';
                        echo '<th>' . Select2::widget(TypeRaw::getSelect2FieldConfigRaw($searchModel)) . '</th>';
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
                                echo '<td>' . HtmlPurifier::process($dataProviderModel->id) . '</td>';
                                echo '<td>' . HtmlPurifier::process($dataProviderModel->price) . '</td>';
                                echo '<td>' . HtmlPurifier::process($dataProviderModel->factor) . '</td>';
                                echo '<td>' . HtmlPurifier::process($dataProviderModel->qty) . '</td>';
                                echo '<td>' . ($dataProviderModel->seller ? $dataProviderModel->seller->printFullnameAndCode() : '') . '</td>';
                                echo '<td>' . HtmlPurifier::process($dataProviderModel->submitAt) . '</td>';
                                echo '<td>' . HtmlPurifier::process($dataProviderModel->factorAt) . '</td>';
                                echo '<td>' . HtmlPurifier::process($dataProviderModel->des) . '</td>';
                                echo '<td>' . ($dataProviderModel->provider ? $dataProviderModel->provider->printFullnameAndCode() : '') . '</td>';
                                echo '<td>' . ($dataProviderModel->raw ? $dataProviderModel->raw->printNameAndUnit() : '') . '</td>';
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