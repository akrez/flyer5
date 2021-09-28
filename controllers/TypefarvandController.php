<?php

namespace app\controllers;

use app\models\TypeFarvand;

class TypefarvandController extends TypeController
{
    public static function getCategoryClass()
    {
        return TypeFarvand::getCategoryClass();
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
