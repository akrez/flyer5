<?php

namespace app\controllers;

use app\models\TypeReseller;

class TyperesellerController extends TypeController
{
    public static function getCategoryClass()
    {
        return TypeReseller::class;
    }

    public function actionIndex($id = null)
    {
        return $this->index($id);
    }

    public function actionSuggest()
    {
        return $this->suggest();
    }
}
