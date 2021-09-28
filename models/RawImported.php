<?php

namespace app\models;

use app\components\Helper;
use Yii;

/**
 * This is the model class for table "raw_imported".
 *
 * @property int $id
 * @property int $price
 * @property string $factor
 * @property int $qty
 * @property int $sellerId
 * @property string $submitAt
 * @property string $factorAt
 * @property string $des
 * @property int $providerId
 * @property int $rawId
 *
 * @property Raw $raw
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
            [['rawId'], 'exist', 'skipOnError' => true, 'targetClass' => Raw::class, 'targetAttribute' => ['rawId' => 'id']],
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

    public function getRaw()
    {
        return $this->hasOne(Raw::class, ['id' => 'rawId']);
    }

    public function getProvider()
    {
        return $this->hasOne(Hrm::class, ['id' => 'providerId']);
    }

    public function attributeLabels()
    {
        return ['providerId' => 'وارد کننده'] + parent::attributeLabels();
    }

}
