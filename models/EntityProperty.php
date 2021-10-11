<?php

namespace app\models;

use app\models\Entity;
use app\models\TypeProperty;

class EntityProperty extends Entity
{
    public const BARCODE_MAX = '50510000';

    public function rules()
    {
        return $this->defaultRules();
    }

    public static function getCategoryClass()
    {
        return TypeProperty::getCategoryClass();
    }

    public static function validQuery($categoryId = null, $barcode = null)
    {
        return parent::validQuery(TypeProperty::getCategoryClass(), $barcode);
    }

    public static function modelTitle()
    {
        return 'لیست اموال';
    }

    public function attributeLabels()
    {
        return [
            'typeId' => 'اموال',
        ] + parent::attributeLabels();
    }

    public static function getSelect2FieldConfigType($model, $url = '')
    {
        $url = TypeProperty::getSuggestUrl();
        return parent::getSelect2FieldConfigType($model, $url);
    }
}
