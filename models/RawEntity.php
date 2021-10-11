<?php

namespace app\models;

use app\models\Type;
use app\models\Entity;
use app\models\RawType;
use Exception;
use Throwable;
use Yii;

/**
 * This is the model class for table "raw_entity".
 *
 * @property int $id
 * @property string $entityBarcode
 * @property int $qty
 * @property int $rawId
 *
 * @property Entity $entity
 * @property Type $raw
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
            [['entityBarcode', 'rawId'], 'required'],
            [['entityBarcode'], 'string', 'max' => 11],
            [['rawId'], 'integer'],
            [['qty'], 'double', 'min' => 0],
            [['entityBarcode'], 'exist', 'skipOnError' => true, 'targetClass' => Entity::class, 'targetAttribute' => ['entityBarcode' => 'barcode']],
            [['rawId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['rawId' => 'id']],
        ];
    }

    /**
     * Gets query for [[EntityBarcode0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntity()
    {
        return $this->hasOne(Entity::class, ['barcode' => 'entityBarcode']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRaw()
    {
        return $this->hasOne(Type::class, ['id' => 'rawId']);
    }

    public static function batchInsert($entity, $entityBarcodes)
    {
        try {
            $rows = [];
            $columns = array_keys((new RawEntity())->attributes);
            $rawTypes = RawType::findAll(['typeId' => $entity->typeId]);
            if (empty($rawTypes)) {
                return true;
            }
            foreach ($rawTypes as $rawType) {
                foreach ($entityBarcodes as $entityBarcode) {
                    $row = [];
                    foreach ($columns as $columnIndex => $column) {
                        if ($column == 'entityBarcode') {
                            $row[$columnIndex] = $entityBarcode;
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
        } catch (Throwable $ex) {
            Yii::$app->session->setFlash('danger', $ex->getMessage());
        }
        return false;
    }
}
