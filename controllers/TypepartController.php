<?php

namespace app\controllers;

use app\models\TypePart;

class TypepartController extends TypeController
{
    public static function getCategoryClass()
    {
        return TypePart::class;
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
