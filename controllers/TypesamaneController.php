<?php

namespace app\controllers;

use app\models\TypeSamane;

class TypesamaneController extends TypeController
{
    public static function getCategoryClass()
    {
        return TypeSamane::class;
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
