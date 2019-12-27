<?php

namespace app\models;

/**
 * This is the model class for table "raw".
 *
 * @property int $id
 * @property string $name
 * @property string $shortname
 * @property string $unit
 * @property string $des
 */
class Raw extends ActiveRecord
{

    public static function tableName()
    {
        return 'raw';
    }

    public static function modelName()
    {
        return 'انواع مواد خام';
    }

    public function rules()
    {
        return [
            [['name', 'shortname', 'unit'], 'required'],
            [['name', 'shortname', 'unit'], 'string', 'max' => 63],
            [['des'], 'string', 'max' => 255],
            [['shortname'], 'unique'],
            [['shortname'], 'unique', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['shortname' => 'shortname']],
        ];
    }

}
