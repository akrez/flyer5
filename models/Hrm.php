<?php

namespace app\models;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use app\components\Helper;
use app\models\ActiveRecord;

/**
 * This is the model class for table "hrm".
 *
 * @property int $id
 * @property string $updatedAt
 * @property string $createdAt
 * @property string $fullname
 * @property string $fatherName
 * @property string $code
 * @property string $mobile
 * @property int $gender
 * @property string $nationalCode
 * @property string $birthdate
 * @property int $role
 * @property string $des
 */
class Hrm extends ActiveRecord
{

    public static $genderList = [
        1 => 'مرد',
        2 => 'زن',
    ];
    public static $roleList = [
        1 => 'کارمند',
        2 => 'فروشنده',
        3 => 'خریدار',
        4 => 'شرکت',
        5 => 'پیمانکار',
    ];

    public static function modelTitle()
    {
        return 'منابع انسانی';
    }

    public static function tableName()
    {
        return 'hrm';
    }

    public function validateDate($attribute, $params)
    {
        if ($this->$attribute = Helper::formatDate($this->$attribute)) {
        } else {
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function validateNationalCode($attribute, $params)
    {
        if (!Helper::validateNationalCode($this->$attribute)) {
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function printGender()
    {
        if (isset(self::$genderList[$this->gender])) {
            return self::$genderList[$this->gender];
        }
        return '';
    }

    public function printRole()
    {
        if (isset(self::$roleList[$this->role])) {
            return self::$roleList[$this->role];
        }
        return '';
    }

    public function rules()
    {
        return [
            [['fullname', 'code', 'role'], 'required'],
            [['gender', 'role'], 'integer'],
            [['updatedAt', 'createdAt'], 'string', 'max' => 19],
            [['fullname', 'fatherName'], 'string', 'max' => 63],
            [['code', 'mobile', 'nationalCode', 'birthdate'], 'string', 'max' => 15],
            [['des'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['nationalCode'], 'unique'],
            //
            [['gender'], 'in', 'range' => array_keys(self::$genderList)],
            [['role'], 'in', 'range' => array_keys(self::$roleList)],
            [['mobile'], 'match', 'pattern' => '/^[0-9]{4}-[0-9]{7}$/'],
            [['birthdate'], 'match', 'pattern' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'],
            [['birthdate'], 'validateDate'],
            [['nationalCode'], 'match', 'pattern' => '/^[0-9]{3}-[0-9]{6}-[0-9]{1}$/'],
            [['nationalCode'], 'validateNationalCode'],
            [['nationalCode'], 'default', 'value' => null],
        ];
    }

    public static function getSellerList()
    {
        return Hrm::find()->select('fullname')->where(['role' => 2])->indexBy('id')->column();
    }

    public static function validQuery($id = null)
    {
        $query = Hrm::find();
        $query->andFilterWhere(['id' => $id]);
        return $query;
    }

    public function printFullnameAndCode()
    {
        return $this->fullname . ' (' . $this->code . ')';
    }

    public static function getSelect2FieldConfigProvider($model)
    {
        if ($model->hasAttribute('barcode')) {
            $id = Html::getInputId($model, 'providerId') . '-' . $model->barcode;
        } else {
            $id = Html::getInputId($model, 'providerId') . '-' . $model->id;
        }
        return Helper::getSelect2FieldConfig($model, 'providerId', Url::toRoute(['hrm/suggest']), [
            'data' => ($model->providerId && $model->provider ? [$model->provider->id => $model->provider->printFullnameAndCode()] : []),
            'placeholder' => $model->getAttributeLabel('providerId'),
            'id' => $id,
        ]);
    }

    public static function getSelect2FieldConfigSeller($model)
    {
        if ($model->hasAttribute('barcode')) {
            $id = Html::getInputId($model, 'sellerId') . '-' . $model->barcode;
        } else {
            $id = Html::getInputId($model, 'sellerId') . '-' . $model->id;
        }
        return Helper::getSelect2FieldConfig($model, 'sellerId', Url::toRoute(['hrm/suggest', 'role' => 2]), [
            'data' => ($model->sellerId && $model->seller ? [$model->seller->id => $model->seller->printFullnameAndCode()] : []),
            'placeholder' => $model->getAttributeLabel('sellerId'),
            'id' => $id,
        ]);
    }
}
