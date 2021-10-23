<?php

use app\models\Hrm;
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

$this->title = $newModel::modelTitle();
MaskedInputAsset::register($this);
DatepickerAsset::register($this);
GridViewAsset::register($this);

$sort = $dataProvider->sort;
$modelClass = new Hrm();

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
jQuery('.nationalCodeInputmask').inputmask({'mask':'999-999999-9'});
jQuery('.mobileInputmask').inputmask({'mask':'9999-9999999'});
$('.hrmBirthdateDatepicker').persianDatepicker({
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
                        <th><?= $sort->link('code', ['label' => $modelClass->getAttributeLabel('code')]) ?></th>
                        <th><?= $sort->link('role', ['label' => $modelClass->getAttributeLabel('role')]) ?></th>
                        <th><?= $sort->link('fullname', ['label' => $modelClass->getAttributeLabel('fullname')]) ?></th>
                        <th><?= $sort->link('fatherName', ['label' => $modelClass->getAttributeLabel('fatherName')]) ?></th>
                        <th><?= $sort->link('gender', ['label' => $modelClass->getAttributeLabel('gender')]) ?></th>
                        <th><?= $sort->link('nationalCode', ['label' => $modelClass->getAttributeLabel('nationalCode')]) ?></th>
                        <th><?= $sort->link('birthdate', ['label' => $modelClass->getAttributeLabel('birthdate')]) ?></th>
                        <th><?= $sort->link('mobile', ['label' => $modelClass->getAttributeLabel('mobile')]) ?></th>
                        <th><?= $sort->link('des', ['label' => $modelClass->getAttributeLabel('des')]) ?></th>
                        <th></th>
                    </tr>
                    <tr id="table-filters" class="info">
                        <th><?= Html::activeInput('code', $searchModel, 'code', ['class' => 'form-control']) ?></th>
                        <th><?= Html::activeDropDownList($searchModel, 'role', Hrm::$roleList, ['class' => 'form-control', 'prompt' => '']) ?></th>
                        <th><?= Html::activeInput('fullname', $searchModel, 'fullname', ['class' => 'form-control']) ?></th>
                        <th><?= Html::activeInput('fatherName', $searchModel, 'fatherName', ['class' => 'form-control']) ?></th>
                        <th><?= Html::activeDropDownList($searchModel, 'gender', Hrm::$genderList, ['class' => 'form-control', 'prompt' => '']) ?></th>
                        <th><?= Html::activeInput('nationalCode', $searchModel, 'nationalCode', ['class' => 'form-control']) ?></th>
                        <th><?= Html::activeInput('birthdate', $searchModel, 'birthdate', ['class' => 'form-control']) ?></th>
                        <th><?= Html::activeInput('mobile', $searchModel, 'mobile', ['class' => 'form-control']) ?></th>
                        <th><?= Html::activeInput('des', $searchModel, 'des', ['class' => 'form-control']) ?></th>
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
                                <td>
                                    <?= HtmlPurifier::process($dataProviderModel->code) ?>
                                </td>
                                <td>
                                    <?= $dataProviderModel->printRole() ?>
                                </td>
                                <td>
                                    <?= HtmlPurifier::process($dataProviderModel->fullname) ?>
                                </td>
                                <td>
                                    <?= HtmlPurifier::process($dataProviderModel->fatherName) ?>
                                </td>
                                <td>
                                    <?= $dataProviderModel->printGender() ?>
                                </td>
                                <td>
                                    <?= HtmlPurifier::process($dataProviderModel->nationalCode) ?>
                                </td>
                                <td>
                                    <?= HtmlPurifier::process($dataProviderModel->birthdate) ?>
                                </td>
                                <td>
                                    <?= HtmlPurifier::process($dataProviderModel->mobile) ?>
                                </td>
                                <td>
                                    <?= HtmlPurifier::process($dataProviderModel->des) ?>
                                </td>
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
                            <tr class="" style="<?= $displayStyle ?>" id="<?= "row-update-" . $dataProviderModel->id ?>">
                                <td colspan="10">
                                    <?= $this->render('_form', ['model' => $dataProviderModel]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php } else { ?>
                        <tr class="danger">
                            <td colspan="10">
                                <?= Yii::t('yii', 'No results found.') ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr class="success">
                        <td colspan="10">
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