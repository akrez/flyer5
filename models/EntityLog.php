<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity_log".
 *
 * @property int $id
 * @property string|null $updatedAt
 * @property string|null $createdAt
 * @property string|null $oldValue
 * @property string|null $newValue
 * @property string|null $des
 * @property string $entityBarcode
 *
 * @property Entity $entity
 */
class EntityLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entity_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['des'], 'string', 'max' => 255],
        ];
    }

    public static function modelTitle()
    {
        return 'تاریخچه تغییرات';
    }

    /**
     * Gets query for [[Entity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntity()
    {
        return $this->hasOne(Entity::class, ['barcode' => 'entityBarcode']);
    }
}
