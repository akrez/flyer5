<?php

namespace app\models;

use app\components\Helper;
use app\models\Type;
use Exception;
use Yii;

/**
 * This is the model class for table "entity".
 *
 * @property string $factor
 * @property int $qty
 * @property int $sellerId
 * @property string $submitAt
 * @property string $factorAt
 * @property string $des
 * @property int $providerId
 * @property int $qc
 * @property int $qa
 * @property int $price
 * @property int $id
 * @property string $productAt
 * @property string $categoryId
 * @property int $parentId
 * @property int $typeId
 *
 * @property Hrm $provider
 * @property Type $type
 * @property Hrm $seller
 * @property Entity $parent
 * @property Entity[] $entities
 */
class Entity extends ActiveRecord
{

    public $_count = 1;

    public static function tableName()
    {
        return 'entity';
    }

    public function defaultRules()
    {
        return [
            [['id', 'categoryId', 'typeId'], 'required'],
            [['id', 'qc', 'qa', 'price', 'providerId', 'parentId', 'sellerId', 'typeId', '_count'], 'integer'],
            [['factor'], 'string', 'max' => 63],
            [['des'], 'string', 'max' => 255],
            [['submitAt', 'factorAt', 'productAt'], 'string', 'max' => 19],
            [['categoryId'], 'string', 'max' => 12],
            [['id'], 'unique'],
            [['providerId'], 'exist', 'skipOnError' => true, 'targetClass' => Hrm::className(), 'targetAttribute' => ['providerId' => 'id']],
            [['sellerId'], 'exist', 'skipOnError' => true, 'targetClass' => Hrm::className(), 'targetAttribute' => ['sellerId' => 'id']],
            [['parentId'], 'exist', 'skipOnError' => true, 'targetClass' => Entity::className(), 'targetAttribute' => ['parentId' => 'id']],
            //
            [['parentId'], 'compare', 'operator' => '!=', 'compareAttribute' => 'id'],
            [['submitAt', 'factorAt', 'productAt'], 'validateDate'],
            [['submitAt', 'factorAt', 'productAt'], 'match', 'pattern' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'],
        ];
    }

    public function rules()
    {
        $rules = [];
        if ($this->categoryId == Type::CATEGORY_PART) {
            $rules = [
                [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['typeId' => 'id'], 'filter' => ['categoryId' => Type::CATEGORY_PART]],
                [['providerId'], 'required'],
            ];
        }
        if ($this->categoryId == Type::CATEGORY_SAMANE) {
            $rules = [
                [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['typeId' => 'id'], 'filter' => ['categoryId' => Type::CATEGORY_SAMANE]],
            ];
        }
        if ($this->categoryId == Type::CATEGORY_RESELLER) {
            $rules = [
                [['price', 'factor', 'providerId', 'sellerId',], 'required'],
            ];
        }
        return array_merge($this->defaultRules(), $rules);
    }

    public function attributeLabels()
    {
        $labels = [];
        if ($this->categoryId == Type::CATEGORY_FARVAND) {
            $labels = [
                'typeId' => 'فروند',
            ];
        }
        if ($this->categoryId == Type::CATEGORY_PART) {
            $labels = [
                'typeId' => 'قطعه',
            ];
        }
        if ($this->categoryId == Type::CATEGORY_SAMANE) {
            $labels = [
                'typeId' => 'سامانه'
            ];
        }
        if ($this->categoryId == Type::CATEGORY_RESELLER) {
            $labels = [
                'typeId' => 'ریسلر',
                'providerId' => 'وارد کننده',
            ];
        }
        return ['id' => 'بارکد'] + $labels + ['providerId' => 'وارد کننده / سازنده'] + parent::attributeLabels();
    }

    public static function suggestId($categoryId)
    {
        $id = Entity::find()->select('id')->where(['categoryId' => $categoryId])->orderBy(['id' => SORT_DESC])->scalar();
        if (empty($id)) {
            switch ($categoryId) {
                case Type::CATEGORY_FARVAND:
                    return "20200001";
                case Type::CATEGORY_PART:
                    return "10100051";
                case Type::CATEGORY_RESELLER:
                    return "40400001";
                case Type::CATEGORY_SAMANE:
                    return "30300001";
            }
        }
        return intval($id) + 1;
    }

    public static function modelTitle($categoryId = null)
    {
        switch ($categoryId) {
            case Type::CATEGORY_FARVAND:
                return 'لیست فروند';
            case Type::CATEGORY_PART:
                return 'لیست قطعات';
            case Type::CATEGORY_RESELLER:
                return 'لیست ریسلر';
            case Type::CATEGORY_SAMANE:
                return 'لیست سامانه';
        }
        return 'موجودیت‌ها';
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

    public function getProvider()
    {
        return $this->hasOne(Hrm::className(), ['id' => 'providerId']);
    }

    public function getType()
    {
        return $this->hasOne(Type::className(), ['id' => 'typeId']);
    }

    public function getSeller()
    {
        return $this->hasOne(Hrm::className(), ['id' => 'sellerId']);
    }

    public function getParent()
    {
        return $this->hasOne(Entity::className(), ['id' => 'parentId']);
    }

    public function getEntities()
    {
        return $this->hasMany(Entity::className(), ['parentId' => 'id']);
    }

    public static function resellerBatchInsert($model, $ids)
    {
        try {
            $attibutes = $model->attributes;
            $columns = array_keys($attibutes);
            $rows = [];
            foreach ($ids as $id) {
                $row = [];
                foreach ($columns as $columnIndex => $column) {
                    $row[$columnIndex] = ($column == 'id' ? $id : $attibutes[$column]);
                }
                $rows[] = $row;
            }
            return Yii::$app->db->createCommand()->batchInsert(self::tableName(), $columns, $rows)->execute() && RawEntity::resellerBatchInsert($model, $ids);
        } catch (Exception $ex) {
            Yii::$app->session->setFlash('danger', $ex->getMessage());
        }
        return false;
    }

    public function findChildModels(&$models, &$childsMap, &$parentMap)
    {
        $id = $this->id;
        //
        $models[$id] = $this;
        $childsMap[$id] = [];
        $parentMap[$id] = $this->parentId;
        //
        $childs = Entity::find()->where(['parentId' => $id])->all();
        foreach ($childs as $child) {
            if (!isset($models[$child->id])) {
                $childsMap[$id][] = $child->id;
                $child->findChildModels($models, $childsMap, $parentMap);
            }
        }
    }

}
