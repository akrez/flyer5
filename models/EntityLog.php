<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity_log".
 *
 * @property int $id
 * @property string|null $updatedAt
 * @property string|null $createdAt
 * @property string $entityAttribute
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
        return 'تغییرات';
    }

    public static function validQuery($id = null, $entityBarcode = null)
    {
        $query = static::find();
        $query->andFilterWhere(['id' => $id]);
        $query->andFilterWhere(['entityBarcode' => $entityBarcode]);
        return $query;
    }

    public static function log($entityAttribute, $newModel, $oldModel = null)
    {
        if ($newModel->{$entityAttribute} != $oldModel->{$entityAttribute}) {
            $entityLog = new EntityLog();
            $entityLog->oldValue = ($oldModel ? $oldModel->{$entityAttribute} : null);
            $entityLog->newValue = $newModel->{$entityAttribute};
            $entityLog->entityBarcode = $newModel->barcode;
            $entityLog->entityAttribute = $entityAttribute;
            return $entityLog->save();
        }
        return null;
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

    public static function getGridViewColumns($visableAttributes, $searchModel, $newModel)
    {
        return [
            'entityBarcode',
            'createdAt',
            'updatedAt',
            'oldValue',
            'newValue',
            [
                'attribute' => 'entityAttribute',
                'value' => function ($model, $key, $index, $grid) {
                    return $model->getAttributeLabel($model->entityAttribute);
                },
            ],
            'des',
        ];
    }
}
