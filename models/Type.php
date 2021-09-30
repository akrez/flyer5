<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

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
 * @property Type $parent
 * @property RawType[] $rawTypes
 * @property Type[] $types
 */
class Type extends ActiveRecord
{
    const CATEGORY_FARVAND = 'FARVAND';
    const CATEGORY_PART = 'PART';
    const CATEGORY_SAMANE = 'SAMANE';
    const CATEGORY_RESELLER = 'RESELLER';

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
        return $this->defaultRules();
    }

    public function defaultRules()
    {
        return [
            [['name', 'shortname'], 'string', 'max' => 63],
            [['name', 'shortname'], 'required'],
            [['des'], 'string', 'max' => 255],
            [['shortname'], 'unique'], //
        ];
    }

    /**
     * Gets query for [[Entities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntities()
    {
        return $this->hasMany(Entity::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Type::class, ['id' => 'parentId']);
    }

    /**
     * Gets query for [[RawTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRawTypes()
    {
        return $this->hasMany(RawType::class, ['typeId' => 'id']);
    }

    /**
     * Gets query for [[Types]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypes()
    {
        return $this->hasMany(Type::class, ['parentId' => 'id']);
    }

    public static function modelTitle($categoryId = null)
    {
        return 'موجودیت‌ها';
    }

    public static function getCategoryClass()
    {
        return static::class;
    }

    public static function modelName()
    {
        return static::modelTitle();
    }

    public static function printCategory($categoryId = null)
    {
        return '';
    }

    public function attributeLabels()
    {
        return [
            'typeId' => static::printCategory(),
        ] + parent::attributeLabels();
    }

    public static function validQuery($id = null)
    {
        $query = static::find();
        $query->andFilterWhere(['id' => $id]);
        return $query;
    }

    public function printNameAndShortname()
    {
        return $this->name . ' (' . $this->shortname . ')';
    }

    public function printNameAndUnit()
    {
        return $this->name . ' (' . $this->unit . ')';
    }

    public static function getSelect2FieldConfigParent($model)
    {
        return [
            'model' => $model,
            'attribute' => 'parentId',
            'data' => ($model->parentId && $model->parent ? [$model->parent->id => $model->parent->printNameAndShortname()] : []),
            'options' => [
                'id' => Html::getInputId($model, 'parentId') . '-' . $model->id,
                'dir' => 'rtl',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => Url::toRoute(['typefarvand/suggest']),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                ]
            ],
        ];
    }

    public static function getSelect2FieldConfigRaw($model)
    {
        return [
            'model' => $model,
            'attribute' => 'rawId',
            'data' => ($model->rawId && $model->raw ? [$model->raw->id => $model->raw->name . ' (' . $model->raw->unit . ')'] : []),
            'options' => [
                'placeholder' => $model->getAttributeLabel('rawId'),
                'id' => Html::getInputId($model, 'rawId') . '-' . $model->id,
                'dir' => 'rtl',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => Url::toRoute(['raw/suggest']),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                ]
            ],
        ];
    }
}
