<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\models\Entity;
use app\models\TypeSamane;

class EntitySamane extends Entity
{
    public const BARCODE_MAX = '30300001';

    public function rules()
    {
        $rules = [
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => TypeSamane::class, 'targetAttribute' => ['typeId' => 'id'], 'filter' => ['categoryId' => TypeSamane::class]],
        ];
        return array_merge($this->defaultRules(), $rules);
    }

    public static function getCategoryClass()
    {
        return TypeSamane::getCategoryClass();
    }

    public static function validQuery($categoryId = null, $barcode = null)
    {
        return parent::validQuery(TypeSamane::getCategoryClass(), $barcode);
    }

    public static function modelTitle()
    {
        return 'لیست سامانه';
    }

    public function attributeLabels()
    {
        return [
            'typeId' => 'سامانه',
        ] + parent::attributeLabels();
    }

    public static function getSelect2FieldConfigType($model, $url = '')
    {
        $url = TypeSamane::getSuggestUrl();
        return parent::getSelect2FieldConfigType($model, $url);
    }
}
