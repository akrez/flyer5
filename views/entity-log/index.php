<?php

use app\models\EntityLog;
use yii\web\View;
use app\models\Model;
use yii\widgets\Pjax;
use app\widgets\Alert;
use app\models\TypeRaw;
use yii\widgets\LinkPager;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridViewAsset;
use yii\helpers\HtmlPurifier;

$this->title = $newModel::modelTitle() . ': ' . $parentModel->barcode;

$this->registerCss("
.table th {
    vertical-align: top !important;
}
.table td {
    vertical-align: middle !important;
}
");

$this->registerJs("
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
});
", View::POS_READY);
?>

<h3 class="pb20"><?= Html::encode($this->title) ?></h3>
<?php
Pjax::begin([
    'id' => "hrm-pjax",
    'timeout' => false,
    'enablePushState' => false,
]);
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
                'options' => ['class' => 'table-responsive'],
                'columns' => array_merge(EntityLog::getGridViewColumns([], $searchModel, new EntityLog()), [
                    [
                        'value' => function ($dataProviderModel) use ($model, $state) {
                            $btnClass = ' btn-default ';
                            if ($model && $model->id == $dataProviderModel->id && $state == 'update') {
                                $btnClass = ' btn-warning ';
                            }
                            return Html::button(Yii::t('app', 'Update'), ['class' => 'btn btn-block' . $btnClass, 'toggle' => "#row-update-" . $dataProviderModel->id]);
                        },
                        'format' => 'raw',
                    ],
                ]),
                'afterRow' => function ($dataProviderModel) use ($model, $state, $parentModel) {
                    $displayStyle = 'display: none;';
                    if ($model && $model->id == $dataProviderModel->id) {
                        $dataProviderModel = $model;
                        $displayStyle = 'display: table-row;';
                    }
                    //
                    ob_start();
                    echo $this->render('_form', ['model' => $dataProviderModel, 'parentModel' => $parentModel]);
                    $formContent = ob_get_contents();
                    ob_end_clean();
                    //
                    return Html::beginTag('tr', [
                        'style' => $displayStyle,
                        'id' => "row-update-" . $dataProviderModel->id,
                    ]) . '<td colspan="16"> ' . $formContent . ' </td></tr>';
                },
            ]);
            ?>
        </div>
    </div>
</div>
<?php
Pjax::end();
?>