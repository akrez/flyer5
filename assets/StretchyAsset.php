<?php

namespace app\assets;

use yii\web\AssetBundle;

class StretchyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'cdn/js/stretchy.min.js',
    ];
}
