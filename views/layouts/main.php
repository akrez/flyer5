<?php

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
//
$this->registerJs('
$(".sidebar-toggle").click(function() {
    $.ajax({url: "' . Url::to(['site/sidebar'], true) . '"});
});
');
$this->registerCss("
.splash-style {
    background-image: url('" . Yii::getAlias('@web/cdn/img/loading.svg') . "');
}
");
//
$__history = (array) Yii::$app->session->get('__history', []);
$__controllerId = Yii::$app->controller->id;
$__actionId = Yii::$app->controller->action->id;
if (
    isset($__history[0]) &&
    isset($__history[0]['controllerId']) &&
    isset($__history[0]['actionId']) &&
    ($__history[0]['controllerId'] == $__controllerId && $__history[0]['actionId'] == $__actionId)
) {
} elseif (Yii::$app->errorHandler->errorAction == $__controllerId . '/' . $__actionId) {
} else {
    array_unshift($__history, [
        'controllerId' => Yii::$app->controller->id,
        'actionId' => Yii::$app->controller->action->id,
        'content' => $this->title,
        'url' => Yii::$app->request->url,
    ]);
    $__history = array_slice($__history, 0, 5);
}
Yii::$app->session->set('__history', $__history);
//
$this->title = ($this->title ? $this->title : Yii::$app->name);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Yii::$app->view->registerLinkTag(['rel' => 'icon', 'href' => Yii::getAlias('@web/favicon.ico')]) ?>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="skin-green sidebar-mini <?= Yii::$app->session->get('__sidebar', false) ? 'sidebar-collapse' : '' ?>">
    <?php $this->beginBody() ?>
    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">
            <!-- Logo -->
            <?= $this->render('logo') ?>
            <!-- Header Navbar -->
            <?= $this->render('navbar') ?>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <?php
        if (Yii::$app->user->isGuest) {
        } else {
            echo $this->render('left');
        }
        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <div class="col-xs-12">
                <br />
                <?= Alert::widget() ?>
            </div>
            <!-- Main content -->
            <section class="content">
                <?= $content ?>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>