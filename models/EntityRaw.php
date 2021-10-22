<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\models\Entity;
use app\models\TypeRaw;

class EntityRaw extends Entity
{
    public const BARCODE_MAX = '60610001';

    public function rules()
    {
        $rules = [
            [['qty',], 'required'],
            [['qty',], 'integer'],
        ];
        return array_merge($this->defaultRules(), $rules);
    }

    public static function getCategoryClass()
    {
        return TypeRaw::getCategoryClass();
    }

    public static function validQuery($categoryId = null, $barcode = null)
    {
        return parent::validQuery(TypeRaw::getCategoryClass(), $barcode);
    }

    public static function modelTitle()
    {
        return 'مواد اولیه وارد شده';
    }

    public function attributeLabels()
    {
        return [
            'qty' => 'مقدار',
            'providerId' => 'وارد کننده',
        ] + parent::attributeLabels();
    }

    public static function getSelect2FieldConfigType($model, $url = '')
    {
        $url = TypeRaw::getSuggestUrl();
        return parent::getSelect2FieldConfigType($model, $url);
    }
}
