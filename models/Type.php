<?php

namespace app\models;

use app\components\Helper;
use Exception;
use Throwable;
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
        return 'اطلاعات پایه';
    }

    public static function getCategoryClass()
    {
        return static::class;
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

    public static function getSuggestUrl()
    {
        return '';
    }

    public static function getSelect2FieldConfigParent($model)
    {
        if ($model->hasAttribute('barcode')) {
            $id = Html::getInputId($model, 'parentId') . '-' . $model->barcode;
        } else {
            $id = Html::getInputId($model, 'parentId') . '-' . $model->id;
        }
        return Helper::getSelect2FieldConfig($model, 'parentId', TypeFarvand::getSuggestUrl(), [
            'data' => ($model->parentId && $model->parent ? [$model->parent->id => $model->parent->printNameAndShortname()] : []),
            'placeholder' => '',
            'id' => $id,
        ]);
    }

    public static function getSelect2FieldConfigRaw($model)
    {
        if ($model->hasAttribute('barcode')) {
            $id = Html::getInputId($model, 'rawId') . '-' . $model->barcode;
        } else {
            $id = Html::getInputId($model, 'rawId') . '-' . $model->id;
        }
        return Helper::getSelect2FieldConfig($model, 'rawId', TypeRaw::getSuggestUrl(), [
            'data' => ($model->rawId && $model->raw ? [$model->raw->id => $model->raw->printNameAndUnit()] : []),
            'placeholder' => '',
            'id' => $id,
        ]);
    }

    public static function batchInsertByNames($categoryId, $names)
    {
        $names = array_map('trim', $names);
        $names = array_filter($names);
        $names = array_unique($names);
        //
        $errors = [];
        //
        $columns = array_keys((new Type)->attributes);
        //
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $rows = [];
            foreach ($names as $name) {
                $newModel = new Type();
                $newModel->name = $name;
                $newModel->shortname = $name;
                $newModel->categoryId = $categoryId;
                if ($newModel->validate()) {
                    $row = [];
                    foreach ($columns as $column) {
                        $row[$column] = $newModel->{$column};
                    }
                    $rows[] = $row;
                } else {
                    $errors[] = $name;
                }
                unset($newModel);
            }
            if ($columns && $rows) {
                $status = Yii::$app->db->createCommand()->batchInsert(self::tableName(), $columns, $rows)->execute();
                if ($status) {
                    $transaction->commit();
                    return $errors;
                }
            }
        } catch (Throwable $ex) {
            Yii::$app->session->setFlash('danger', $ex->getMessage());
        } catch (Exception $ex) {
            Yii::$app->session->setFlash('danger', $ex->getMessage());
        }
        $transaction->rollBack();
        return $names;
    }
}
