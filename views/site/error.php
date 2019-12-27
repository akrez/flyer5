<?php

use yii\helpers\Html;

$msg = Html::encode($message);
$this->title = $msg;
?>
<div class="page-header">
    <h1> <?= Html::encode($exception->statusCode) ?> <small> <?= nl2br($msg) ?></small></h1>
</div>