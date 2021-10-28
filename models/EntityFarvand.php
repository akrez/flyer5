<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use app\models\Entity;
use app\models\TypeFarvand;

class EntityFarvand extends Entity
{
    public const BARCODE_MAX = '20200001';

    public function rules()
    {
        return $this->defaultRules();
    }

    public static function getCategoryClass()
    {
        return TypeFarvand::getCategoryClass();
    }

    public static function validQuery($categoryId = null, $barcode = null)
    {
        return parent::validQuery(TypeFarvand::getCategoryClass(), $barcode);
    }

    public static function modelTitle()
    {
        return 'لیست فروند';
    }

    public function attributeLabels()
    {
        return [
            'typeId' => 'فروند',
            'providerId' => 'سازنده',
        ] + parent::attributeLabels();
    }

    public static function getSelect2FieldConfigType($model, $url = '')
    {
        $url = TypeFarvand::getSuggestUrl();
        return parent::getSelect2FieldConfigType($model, $url);
    }
}
