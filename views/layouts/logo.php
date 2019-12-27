<?php

use yii\helpers\Url;

?>
<a href="<?= Url::to(['site/index']); ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b><?= mb_substr(Yii::$app->name, 0, 1) ?></b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b><?= Yii::$app->name ?></b></span>
</a>
