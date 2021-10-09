<?php

namespace app\models;

use yii\helpers\Url;

class TypeFarvand extends Type
{
    public function rules()
    {
        return $this->defaultRules();
    }

    public static function modelTitle($categoryId = null)
    {
        return 'انواع فروند';
    }

    public static function printCategory($categoryId = null)
    {
        return 'فروند';
    }

    public function attributeLabels()
    {
        return [
            'typeId' => 'فروند',
            'parentId' => 'فروند مرتبط'
        ] + parent::attributeLabels();
    }

    public static function getSuggestUrl()
    {
        return Url::toRoute(['type/suggest-farvand']);
    }
}
