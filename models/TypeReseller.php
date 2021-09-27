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
class TypeReseller extends ActiveRecord
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
            [['categoryId'], 'required'],
            [['parentId'], 'integer'],
            [['name', 'shortname', 'unit'], 'string', 'max' => 63],
            [['des'], 'string', 'max' => 255],
            [['categoryId'], 'string', 'max' => 12],
            [['shortname'], 'unique'],
            [['parentId'], 'exist', 'skipOnError' => true, 'targetClass' => TypeReseller::className(), 'targetAttribute' => ['parentId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'shortname' => 'Shortname',
            'unit' => 'Unit',
            'des' => 'Des',
            'categoryId' => 'Category ID',
            'parentId' => 'Parent ID',
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
        return $this->hasOne(TypeReseller::className(), ['id' => 'parentId']);
    }

    /**
     * Gets query for [[RawTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRawTypes()
    {
        return $this->hasMany(RawType::className(), ['typeId' => 'id']);
    }

    /**
     * Gets query for [[TypeResellers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypeResellers()
    {
        return $this->hasMany(TypeReseller::className(), ['parentId' => 'id']);
    }
}
