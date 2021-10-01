<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "type".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $shortname
 * @property string|null $unit
 * @property string|null $des
 * @property string $categoryId
 * @property int|null $parentId
 *
 * @property Entity[] $entities
 * @property TypeReseller $parent
 * @property RawType[] $rawTypes
 * @property TypeReseller[] $typeResellers
 */
class TypeProperty extends Type
{
    public function rules()
    {
        return $this->defaultRules();
    }

    public static function modelTitle($categoryId = null)
    {
        return 'انواع اموال';
    }

    public static function printCategory($categoryId = null)
    {
        return 'اموال';
    }
}
