<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\models\Entity;
use app\models\TypePart;

class EntityPart extends Entity
{
    public const BARCODE_MAX = '10100001';

    public function rules()
    {
        $rules = [
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => TypePart::class, 'targetAttribute' => ['typeId' => 'id'], 'filter' => ['categoryId' => TypePart::class]],
            [['providerId'], 'required'],
        ];
        return array_merge($this->defaultRules(), $rules);
    }

    public static function getCategoryClass()
    {
        return TypePart::getCategoryClass();
    }

    public static function validQuery($categoryId = null, $barcode = null)
    {
        return parent::validQuery(TypePart::getCategoryClass(), $barcode);
    }

    public static function modelTitle()
    {
        return 'لیست قطعات';
    }

    public function attributeLabels()
    {
        return [
            'typeId' => 'قطعه',
            'providerId' => 'سازنده',
        ] + parent::attributeLabels();
    }

    public static function getSelect2FieldConfigType($model, $url = '')
    {
        $url = TypePart::getSuggestUrl();
        return parent::getSelect2FieldConfigType($model, $url);
    }
}
