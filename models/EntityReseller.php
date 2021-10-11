<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\models\Entity;
use app\models\TypeReseller;

class EntityReseller extends Entity
{
    public const BARCODE_MAX = '40400001';

    public function rules()
    {
        $rules = [
            [['price', 'factor', 'providerId', 'sellerId',], 'required'],
        ];
        return array_merge($this->defaultRules(), $rules);
    }

    public static function getCategoryClass()
    {
        return TypeReseller::getCategoryClass();
    }

    public static function validQuery($categoryId = null, $barcode = null)
    {
        return parent::validQuery(TypeReseller::getCategoryClass(), $barcode);
    }

    public static function modelTitle()
    {
        return 'لیست ریسلر';
    }

    public function attributeLabels()
    {
        return [
            'typeId' => 'ریسلر',
            'providerId' => 'وارد کننده',
        ] + parent::attributeLabels();
    }

    public static function getSelect2FieldConfigType($model, $url = '')
    {
        $url = TypeReseller::getSuggestUrl();
        return parent::getSelect2FieldConfigType($model, $url);
    }
}
