<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "raw_entity".
 *
 * @property int $id
 * @property int $entityId
 * @property int $qty
 * @property int $rawId
 *
 * @property Entity $entity
 * @property Raw $raw
 */
class RawEntity extends ActiveRecord
{

    public static function tableName()
    {
        return 'raw_entity';
    }

    public static function modelName()
    {
        return 'مواد اوليه';
    }

    public function rules()
    {
        return [
            [['entityId', 'rawId'], 'required'],
            [['entityId', 'rawId'], 'integer'],
            [['qty'], 'double', 'min' => 0],
            [['entityId'], 'exist', 'skipOnError' => true, 'targetClass' => Entity::class, 'targetAttribute' => ['entityId' => 'id']],
            [['rawId'], 'exist', 'skipOnError' => true, 'targetClass' => Raw::class, 'targetAttribute' => ['rawId' => 'id']],
        ];
    }

    public function getEntity()
    {
        return $this->hasOne(Entity::class, ['id' => 'entityId']);
    }

    public function getRaw()
    {
        return $this->hasOne(Raw::class, ['id' => 'rawId']);
    }

    public static function resellerBatchInsert($entity, $entityIds)
    {
        try {
            $rows = [];
            $columns = array_keys((new RawEntity())->attributes);
            $rawTypes = RawType::findAll(['typeId' => $entity->typeId]);
            if (empty($rawTypes)) {
                return true;
            }
            foreach ($rawTypes as $rawType) {
                foreach ($entityIds as $entityId) {
                    $row = [];
                    foreach ($columns as $columnIndex => $column) {
                        if ($column == 'entityId') {
                            $row[$columnIndex] = $entityId;
                        } elseif ($column == 'id') {
                            $row[$columnIndex] = null;
                        } elseif ($rawType->hasAttribute($column)) {
                            $row[$columnIndex] = $rawType->$column;
                        } else {
                            $row[$columnIndex] = null;
                        }
                    }
                    $rows[] = $row;
                }
            }
            return boolval(Yii::$app->db->createCommand()->batchInsert(self::tableName(), $columns, $rows)->execute());
        } catch (Exception $ex) {
            Yii::$app->session->setFlash('danger', $ex->getMessage());
        }
        return false;
    }

}
