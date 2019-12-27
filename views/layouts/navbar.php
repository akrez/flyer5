<?php

use yii\helpers\Url;
?>

<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">منو</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['site/signin']); ?>">
                        <span>ورود</span>
                    </a>
                <?php else: ?>
                    <a href="<?= Url::to(['site/signout']); ?>">
                        <span>خروج</span>
                    </a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>