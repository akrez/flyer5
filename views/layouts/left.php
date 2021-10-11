<?php

use app\models\Type;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Entity;
use app\models\TypeRaw;
use app\models\TypePart;
use app\models\EntityPart;
use app\models\TypeSamane;
use app\models\TypeFarvand;
use app\models\EntitySamane;
use app\models\TypeProperty;
use app\models\TypeReseller;
use app\models\EntityFarvand;
use app\models\EntityProperty;
use app\models\EntityReseller;

$this->registerCss('
    .sidebar-mini.sidebar-collapse .user-panel {
        display: none !important;
        -webkit-transform: translateZ(0);
    }
');
?>

<aside class="main-sidebar">
    <aside class="main-sidebar direction">
        <section class="sidebar">

            <div class="user-panel">
                <div class="header" style="padding-left: 10px;padding-right: 10px;"><?= Html::img('@web/cdn/img/logo.png', ['class' => 'img img-responsive', 'style' => 'min-width: 100%;']); ?></div>
            </div>

            <form action="<?= Url::to(['/relation/index']) ?>" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="parentId" class="form-control" placeholder="<?= Yii::t('app', 'Search') ?>">
                    <span class="input-group-btn">
                        <button type="submit" id="search-btn" class="btn btn-flat">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                </div>
            </form>

            <ul class="sidebar-menu">

                <li class="header">منو اصلی</li>

                <li><a href="<?= Url::toRoute(['/hrm/index']) ?>"><i class="glyphicon glyphicon-user"></i><span>منابع انسانی</span></a></li>

                <li class="treeview">
                    <a href="#"><i class="glyphicon glyphicon-oil"></i><span>مواد خام</span></a>
                    <ul class="treeview-menu" style="right: 49px;">
                        <li><a href="<?= Url::toRoute(['/type/index-raw']) ?>"><?= TypeRaw::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/rawimported/index']) ?>">مواد اولیه وارد شده</a></li>
                    </ul>
                </li>

                <li class="treeview">
                    <a href="#"><i class="glyphicon glyphicon-th"></i><span><?= Type::modelTitle() ?></span></a>
                    <ul class="treeview-menu" style="right: 49px;">
                        <li><a href="<?= Url::toRoute(['/type/index-samane']) ?>"><?= TypeSamane::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/type/index-part']) ?>"><?= TypePart::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/type/index-reseller']) ?>"><?= TypeReseller::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/type/index-farvand']) ?>"><?= TypeFarvand::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/type/index-property']) ?>"><?= TypeProperty::modelTitle() ?></a></li>
                    </ul>
                </li>

                <li class="treeview">
                    <a href="#"><i class="glyphicon glyphicon-list"></i><span><?= Entity::modelTitle() ?></span></a>
                    <ul class="treeview-menu" style="right: 49px;">
                        <li><a href="<?= Url::toRoute(['/entity/index-samane']) ?>"><?= EntitySamane::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/entity/index-part']) ?>"><?= EntityPart::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/entity/index-reseller']) ?>"><?= EntityReseller::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/entity/index-farvand']) ?>"><?= EntityFarvand::modelTitle() ?></a></li>
                        <li><a href="<?= Url::toRoute(['/entity/index-property']) ?>"><?= EntityProperty::modelTitle() ?></a></li>
                    </ul>
                </li>

                <li class="treeview active">
                    <a href="#"><i class="glyphicon glyphicon-time"></i><span>بازدید اخیر</span></a>
                    <ul class="treeview-menu" style="right: 49px;">
                        <?php
                        $__history = Yii::$app->session->get('__history', []);
                        if (!is_array($__history)) {
                            $__history = [];
                        }
                        ?>
                        <?php foreach ($__history as $h) : ?>
                            <?php if (isset($h['url']) && isset($h['content'])) : ?>
                                <li><a href="<?= $h['url'] ?>"><?= $h['content'] ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </li>

            </ul>

        </section>
    </aside>
</aside>