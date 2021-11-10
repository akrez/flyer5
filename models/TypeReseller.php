<?php

namespace app\models;

use Yii;
use app\models\Type;
use yii\helpers\Url;

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
class TypeReseller extends Type
{
    public function rules()
    {
        return array_merge($this->defaultRules(), [
            [['unit'], 'string', 'max' => 63],
            //[['unit'], 'required'],
        ]);
    }

    public static function modelTitle($categoryId = null)
    {
        return 'انواع ریسلر';
    }

    public static function printCategory($categoryId = null)
    {
        return 'ريسلر';
    }

    public static function getSuggestUrl()
    {
        return Url::toRoute(['type/suggest-reseller']);
    }
}
