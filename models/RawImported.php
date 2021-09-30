<?php

namespace app\models;

use app\components\Helper;
use app\models\Hrm;
use app\models\Type;

/**
 * This is the model class for table "raw_imported".
 *
 * @property int $id
 * @property int $price
 * @property string $factor
 * @property int $qty
 * @property int|null $sellerId
 * @property string|null $submitAt
 * @property string|null $factorAt
 * @property string|null $des
 * @property int $providerId
 * @property int $rawId
 *
 * @property Type $raw
 * @property Hrm $provider
 */
class RawImported extends ActiveRecord
{

    public static function tableName()
    {
        return 'raw_imported';
    }

    public static function modelName()
    {
        return 'مواد اولیه وارد شده';
    }

    public function rules()
    {
        return [
            [['price', 'factor', 'qty', 'providerId', 'rawId'], 'required'],
            [['price', 'qty', 'sellerId', 'providerId', 'rawId'], 'integer'],
            [['factor'], 'string', 'max' => 63],
            [['submitAt', 'factorAt'], 'string', 'max' => 19],
            [['des'], 'string', 'max' => 255],
            [['submitAt', 'factorAt'], 'match', 'pattern' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'],
            [['submitAt', 'factorAt'], 'validateDate'],
            [['rawId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['rawId' => 'id'], 'filter' => ['categoryId' => TypeRaw::class]],
            [['providerId'], 'exist', 'skipOnError' => true, 'targetClass' => Hrm::class, 'targetAttribute' => ['providerId' => 'id']],
        ];
    }

    public function validateDate($attribute, $params)
    {
        if ($this->$attribute = Helper::formatDate($this->$attribute)) {
        } else {
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    /**
     * Gets query for [[Provider]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Hrm::class, ['id' => 'providerId']);
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
     * Gets query for [[Seller]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeller()
    {
        return $this->hasOne(Hrm::class, ['id' => 'sellerId']);
    }

    public function attributeLabels()
    {
        return ['providerId' => 'وارد کننده'] + parent::attributeLabels();
    }

    public static function validQuery($id = null)
    {
        $query = static::find();
        $query->andFilterWhere(['id' => $id]);
        return $query;
    }
}
