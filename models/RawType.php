<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "raw_type".
 *
 * @property int $id
 * @property double $qty
 * @property string $des
 * @property int $rawId
 * @property int $typeId
 *
 * @property Raw $raw
 * @property Type $type
 */
class RawType extends ActiveRecord
{

    public static function tableName()
    {
        return 'raw_type';
    }

    public static function modelName()
    {
        return 'مواد اوليه پيش فرض';
    }

    public function rules()
    {
        return [
            [['qty', 'rawId', 'typeId'], 'required'],
            [['qty'], 'number'],
            [['rawId', 'typeId'], 'integer'],
            [['des'], 'string', 'max' => 255],
            [['rawId'], 'exist', 'skipOnError' => true, 'targetClass' => Raw::class, 'targetAttribute' => ['rawId' => 'id']],
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['typeId' => 'id']],
        ];
    }

    public function getRaw()
    {
        return $this->hasOne(Raw::class, ['id' => 'rawId']);
    }

}
