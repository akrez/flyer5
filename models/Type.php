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
 * @property Type $parent
 * @property RawType[] $rawTypes
 * @property Type[] $types
 */
class Type extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'shortname'], 'string', 'max' => 63],
            [['name', 'shortname'], 'required'],
            [['des'], 'string', 'max' => 255],
            [['categoryId'], 'required'],
            [['categoryId'], 'string', 'max' => 12],
            [['shortname'], 'unique'], //
            [['shortname'], 'unique', 'skipOnError' => true, 'targetClass' => Raw::className(), 'targetAttribute' => ['shortname' => 'shortname']],
        ];
    }

    /**
     * Gets query for [[Entities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntities()
    {
        return $this->hasMany(Entity::className(), ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Type::class, ['id' => 'parentId']);
    }

    /**
     * Gets query for [[RawTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRawTypes()
    {
        return $this->hasMany(RawType::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Types]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypes()
    {
        return $this->hasMany(Type::class, ['parentId' => 'id']);
    }

    public static function modelTitle($categoryId = null)
    {
        return 'موجودیت‌ها';
    }

    public static function printCategory($categoryId = null)
    {
        return '';
    }
}
