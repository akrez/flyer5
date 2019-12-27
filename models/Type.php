<?php

namespace app\models;

/**
 * This is the model class for table "type".
 *
 * @property int $id
 * @property string $name
 * @property string $shortname
 * @property string $unit
 * @property string $des
 * @property int $categoryId
 * @property int $parentId
 *
 * @property Type $parent
 * @property Type[] $types
 */
class Type extends ActiveRecord
{

    const CATEGORY_FARVAND = 'FARVAND';
    const CATEGORY_PART = 'PART';
    const CATEGORY_SAMANE = 'SAMANE';
    const CATEGORY_RESELLER = 'RESELLER';

    public static $categoryList = [
        self::CATEGORY_FARVAND => 'فروند',
        self::CATEGORY_PART => 'قطعه',
        self::CATEGORY_SAMANE => 'سامانه',
        self::CATEGORY_RESELLER => 'ريسلر',
    ];

    public static function printCategory($categoryId)
    {
        if (isset(self::$categoryList[$categoryId])) {
            return self::$categoryList[$categoryId];
        }
        return '';
    }

    public static function categoryLabel($categoryId)
    {
        if (isset(self::$categoryList[$categoryId])) {
            return self::$categoryList[$categoryId];
        }
        return '';
    }

    public static function tableName()
    {
        return '{{%type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function defaultRules()
    {
        return [
            [['parentId'], 'integer'],
            [['name', 'shortname', 'unit'], 'string', 'max' => 63],
            [['des'], 'string', 'max' => 255],
            [['shortname'], 'unique'],
            [['shortname'], 'unique', 'skipOnError' => true, 'targetClass' => Raw::className(), 'targetAttribute' => ['shortname' => 'shortname']],
            [['parentId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['parentId' => 'id']],
            [['name', 'shortname'], 'required'],
        ];
    }

    public function rules()
    {
        $rules = [];
        if ($this->categoryId == Type::CATEGORY_PART) {
            $rules = [
                [['parentId'], 'required'],
                [['parentId'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['parentId' => 'id'], 'filter' => ['categoryId' => Type::CATEGORY_FARVAND]],
            ];
        }
        if ($this->categoryId == Type::CATEGORY_RESELLER) {
            $rules = [
                [['unit'], 'required'],
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
                'parentId' => 'فروند مرتبط'
            ];
        }
        if ($this->categoryId == Type::CATEGORY_PART) {
            $labels = [
                'typeId' => 'قطعه',
                'parentId' => 'فروند مرتبط'
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
        return $labels + parent::attributeLabels();
    }

    public static function modelTitle($categoryId = null)
    {
        switch ($categoryId) {
            case Type::CATEGORY_FARVAND:
                return 'انواع فروند';
            case Type::CATEGORY_PART:
                return 'انواع قطعات';
            case Type::CATEGORY_RESELLER:
                return 'انواع ریسلر';
            case Type::CATEGORY_SAMANE:
                return 'انواع سامانه';
        }
        return 'موجودیت‌ها';
    }

    public function getParent()
    {
        return $this->hasOne(Type::className(), ['id' => 'parentId']);
    }

}
