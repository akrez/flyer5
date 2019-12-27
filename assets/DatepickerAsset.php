<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DatepickerAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'cdn/css/persian-datepicker.css',
    ];
    public $js = [
        'cdn/js/persian-date.min.js',
        'cdn/js/persian-datepicker.min.js',
    ];
    public $depends = [
        'app\assets\BootstrapAsset',
    ];

}
