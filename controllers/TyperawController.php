<?php

namespace app\controllers;

use app\models\TypeRaw;

class TyperawController extends TypeController
{
    public static function getCategoryClass()
    {
        return TypeRaw::class;
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
