<?php

namespace app\models;

use Yii;

class Relation extends Entity
{

    public $_entity;

    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'findValidEntityById'],
        ];
    }

    public function findValidEntityById($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $entity = Entity::find()->where(['id' => $this->$attribute])->andWhere(['parentId' => NULL])->one();
            if ($entity) {
                return $this->_entity = $entity;
            }
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
        return $this->_entity = null;
    }

    public function attributeLabels()
    {
        return ['id' => 'بارکد'] + parent::attributeLabels();
    }

    public static function modelName()
    {
        return 'روابط';
    }

}
