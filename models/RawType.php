<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "raw_type".
 *
 * @property int $id
 * @property float $qty
 * @property string|null $des
 * @property int $rawId
 * @property int $typeId
 *
 * @property Type $raw
 * @property Type $type
 */
class RawType extends ActiveRecord
{

    public static function tableName()
    {
        return 'raw_type';
    }

    public static function modelTitle()
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
            [['rawId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['rawId' => 'id']],
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['typeId' => 'id']],
        ];
    }

    /**
     * Gets query for [[Raw]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRaw()
    {
        return $this->hasOne(Type::class, ['id' => 'rawId']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['id' => 'typeId']);
    }

    public static function validQuery($typeId, $id = null)
    {
        $query = static::find();
        $query->andWhere(['typeId' => $typeId]);
        $query->andFilterWhere(['id' => $id]);
        return $query;
    }
}
