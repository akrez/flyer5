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
class TypePart extends Type
{
    public function rules()
    {
        return array_merge($this->defaultRules(), [
            [['parentId'], 'integer'],
            [['parentId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['parentId' => 'id']],
        ]);
    }

    public static function modelTitle($categoryId = null)
    {
        return 'انواع قطعات';
    }

    public static function printCategory($categoryId = null)
    {
        return 'قطعه';
    }

    public function attributeLabels()
    {
        return [
            'parentId' => 'فروند مرتبط'
        ] + parent::attributeLabels();
    }
}
