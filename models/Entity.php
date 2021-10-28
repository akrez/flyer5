<?php

namespace app\models;

use app\models\Hrm;
use app\models\Type;
use yii\helpers\Html;
use app\models\RawType;
use app\models\RawEntity;
use app\components\Helper;
use app\models\ActiveRecord;
use Yii;
use Exception;
use kartik\select2\Select2;
use Throwable;
use yii\helpers\Url;

/**
 * This is the model class for table "entity".
 *
 * @property string|null $factor
 * @property string|null $place
 * @property int|null $sellerId
 * @property string|null $submitAt
 * @property string|null $factorAt
 * @property string|null $des
 * @property int|null $providerId
 * @property int|null $qc
 * @property int|null $qa
 * @property int|null $price
 * @property string $barcode
 * @property string|null $productAt
 * @property string $categoryId
 * @property string|null $parentBarcode
 * @property int $typeId
 * @property int|null $qty
 *
 * @property Hrm $provider
 * @property Type $type
 * @property Hrm $seller
 * @property Entity $parent
 * @property Entity[] $entities
 */
class Entity extends ActiveRecord
{
    public const BARCODE_MAX = '1';

    public $count = 1;

    public static function tableName()
    {
        return 'entity';
    }

    public static function getEntityClass()
    {
        return static::class;
    }

    public static function getCategoryClass()
    {
        return null;
    }

    public function rules()
    {
        return $this->defaultRules();
    }

    public function defaultRules()
    {
        return [
            [['barcode', 'categoryId', 'typeId'], 'required'],
            [['qc', 'qa', 'price', 'providerId', 'sellerId', 'typeId', 'count'], 'integer'],
            [['barcode', 'parentBarcode'], 'string', 'max' => 11],
            [['factor'], 'string', 'max' => 63],
            [['des'], 'string', 'max' => 255],
            [['place', 'submitAt', 'factorAt', 'productAt'], 'string', 'max' => 19],
            [['categoryId'], 'string', 'max' => 36],
            [['barcode'], 'unique'],
            [['providerId'], 'exist', 'skipOnError' => true, 'targetClass' => Hrm::class, 'targetAttribute' => ['providerId' => 'id']],
            [['sellerId'], 'exist', 'skipOnError' => true, 'targetClass' => Hrm::class, 'targetAttribute' => ['sellerId' => 'id']],
            [['parentBarcode'], 'exist', 'skipOnError' => true, 'targetClass' => Entity::class, 'targetAttribute' => ['parentBarcode' => 'barcode']],
            //
            [['parentBarcode'], 'compare', 'operator' => '!=', 'compareAttribute' => 'barcode'],
            [['submitAt', 'factorAt', 'productAt'], 'validateDate'],
            [['submitAt', 'factorAt', 'productAt'], 'match', 'pattern' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'],
        ];
    }

    public static function validQuery($categoryId = null, $barcode = null)
    {
        $query = static::find();
        $query->andFilterWhere(['categoryId' => $categoryId]);
        $query->andFilterWhere(['barcode' => $barcode]);
        return $query;
    }

    public static function suggestBarcode()
    {
        $categoryId = static::getCategoryClass();
        $barcode = self::validQuery($categoryId)->select('barcode')->orderBy(['barcode' => SORT_DESC])->max('barcode');
        if (empty($barcode)) {
            return static::BARCODE_MAX;
        }
        return intval($barcode) + 1;
    }

    public static function modelTitle()
    {
        return 'لیست‌ها';
    }

    public function validateDate($attribute, $params)
    {
        if ($this->$attribute = Helper::formatDate($this->$attribute)) {
        } else {
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert == false) {
            return;
        }
        $rawTypes = (array) RawType::findAll(['typeId' => $this->type->id]);
        foreach ($rawTypes as $rawType) {
            try {
                $rawEntity = new RawEntity();
                $rawEntity->entityId = $this->id;
                $rawEntity->qty = $rawType->qty;
                $rawEntity->rawId = $rawType->rawId;
                $rawEntity->save();
            } catch (Exception $ex) {
            }
        }
    }

    protected static function getSelect2FieldConfigType($model, $url = '')
    {
        if ($model->hasAttribute('barcode')) {
            $id = Html::getInputId($model, 'typeId') . '-' . $model->barcode;
        } else {
            $id = Html::getInputId($model, 'typeId') . '-' . $model->id;
        }
        return Helper::getSelect2FieldConfig($model, 'typeId', $url, [
            'data' => ($model->typeId && $model->type ? [$model->type->id => $model->type->printNameAndShortname()] : []),
            'placeholder' => '',
            'id' => $id,
        ]);
    }

    public static function getSelect2FieldConfigParent($model)
    {
        $id = Html::getInputId($model, 'parentBarcode') . '-' . $model->barcode . '-' . $model->parentBarcode;
        //
        return Helper::getSelect2FieldConfig($model, 'parentBarcode', Url::to(['/entity/suggest/']), [
            'data' => ($model->parentBarcode && $model->parent ? [$model->parent->barcode => $model->parent->barcode] : []),
            'placeholder' => '',
            'id' => $id,
        ]);
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
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['id' => 'typeId']);
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

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Entity::class, ['barcode' => 'parentBarcode']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntities()
    {
        return $this->hasMany(Entity::class, ['parentBarcode' => 'barcode']);
    }

    public static function batchInsert($newModel, $barcodes)
    {
        try {
            $attibutes = $newModel->attributes;
            $columns = array_keys($attibutes);
            $rows = [];
            foreach ($barcodes as $barcode) {
                $row = [];
                foreach ($columns as $columnIndex => $column) {
                    if (mb_strlen($attibutes[$column])) {
                        if ($column == 'barcode') {
                            $row[$columnIndex] = $barcode;
                        } elseif ($column == 'categoryId') {
                            $row[$columnIndex] = $newModel::getCategoryClass();
                        } else {
                            $row[$columnIndex] = $attibutes[$column];
                        }
                    } else {
                        $row[$columnIndex] = null;
                    }
                }
                $rows[] = $row;
            }
            return Yii::$app->db->createCommand()->batchInsert(self::tableName(), $columns, $rows)->execute() && RawEntity::batchInsert($newModel, $barcodes);
        } catch (Throwable $ex) {
            Yii::$app->session->setFlash('danger', $ex->getMessage());
        } catch (Exception $ex) {
            Yii::$app->session->setFlash('danger', $ex->getMessage());
        }
        return false;
    }

    public function findChildModels(&$models, &$childsMap, &$parentMap, &$modelsLevel, $level)
    {
        $barcode = $this->barcode;
        //
        $models[$barcode] = $this;
        $childsMap[$barcode] = [];
        $parentMap[$barcode] = $this->parentBarcode;
        $modelsLevel[$barcode] = $level;
        //
        $childs = Entity::validQuery()->where(['parentBarcode' => $barcode])->all();
        foreach ($childs as $child) {
            if (!isset($models[$child->barcode])) {
                $childsMap[$barcode][] = $child->barcode;
                $child->findChildModels($models, $childsMap, $parentMap, $modelsLevel, $level + 1);
            }
        }
    }

    public static function getGridViewColumns($visableAttributes, $searchModel, $newModel)
    {
        return [
            [
                'attribute' => 'barcode',
                'visible' => !isset($visableAttributes['barcode']) || $visableAttributes['barcode'],
            ],
            [
                'attribute' => 'parentBarcode',
                'value' => function ($model) {
                    if ($model->parentBarcode) {
                        return $model->parent->barcode;
                    }
                },
                'filter' => Select2::widget(Entity::getSelect2FieldConfigParent($searchModel)),
                'contentOptions' => ['class' => 'warning'],
                'visible' => !isset($visableAttributes['parentBarcode']) || $visableAttributes['parentBarcode'],
            ],
            [
                'attribute' => 'typeId',
                'value' => function ($model) {
                    if ($model->categoryId == TypeRaw::getCategoryClass()) {
                        return $model->type->printNameAndUnit();
                    }
                    return $model->type->printNameAndShortname();
                },
                'filter' => Select2::widget($newModel::getSelect2FieldConfigType($searchModel)),
                'visible' => !isset($visableAttributes['typeId']) || $visableAttributes['typeId'],
            ],
            [
                'attribute' => 'qty',
                'visible' => !isset($visableAttributes['qty']) || $visableAttributes['qty'],
            ],
            [
                'attribute' => 'qc',
                'filter' => [
                    0 => Yii::t('yii', 'No'),
                    1 => Yii::t('yii', 'Yes'),
                ],
                'visible' => !isset($visableAttributes['qc']) || $visableAttributes['qc'],
                'format' => 'boolean',
            ],
            [
                'attribute' => 'qa',
                'filter' => [
                    0 => Yii::t('yii', 'No'),
                    1 => Yii::t('yii', 'Yes'),
                ],
                'visible' => !isset($visableAttributes['qa']) || $visableAttributes['qa'],
                'format' => 'boolean',
            ],
            [
                'attribute' => 'place',
                'visible' => !isset($visableAttributes['place']) || $visableAttributes['place'],
            ],
            [
                'attribute' => 'factor',
                'visible' => !isset($visableAttributes['factor']) || $visableAttributes['factor'],
            ],
            [
                'attribute' => 'price',
                'visible' => !isset($visableAttributes['price']) || $visableAttributes['price'],
            ],
            [
                'attribute' => 'submitAt',
                'filter' => Html::activeInput('text', $searchModel, 'submitAt', ['class' => 'form-control entitySubmitatDatepicker']),
                'visible' => !isset($visableAttributes['submitAt']) || $visableAttributes['submitAt'],
            ],
            [
                'attribute' => 'factorAt',
                'filter' => Html::activeInput('text', $searchModel, 'factorAt', ['class' => 'form-control entityFactoratDatepicker']),
                'visible' => !isset($visableAttributes['factorAt']) || $visableAttributes['factorAt'],
            ],
            [
                'attribute' => 'productAt',
                'filter' => Html::activeInput('text', $searchModel, 'productAt', ['class' => 'form-control entityProductatDatepicker']),
                'visible' => !isset($visableAttributes['productAt']) || $visableAttributes['productAt'],
            ],
            [
                'attribute' => 'providerId',
                'value' => function ($model) {
                    if ($model->provider) {
                        return $model->provider->printFullnameAndCode();
                    }
                },
                'filter' => Select2::widget(Hrm::getSelect2FieldConfigProvider($searchModel)),
                'visible' => !isset($visableAttributes['providerId']) || $visableAttributes['providerId'],
            ],
            [
                'attribute' => 'sellerId',
                'value' => function ($model) {
                    if ($model->seller) {
                        return $model->seller->printFullnameAndCode();
                    }
                },
                'filter' => Select2::widget(Hrm::getSelect2FieldConfigSeller($searchModel)),
                'visible' => !isset($visableAttributes['sellerId']) || $visableAttributes['sellerId'],
            ],
            [
                'attribute' => 'des',
                'visible' => !isset($visableAttributes['des']) || $visableAttributes['des'],

            ],
        ];
    }
}
