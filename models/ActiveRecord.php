<?php

namespace app\models;

use app\components\Jdf;
use yii\db\ActiveRecord as BaseActiveRecord;

class ActiveRecord extends BaseActiveRecord
{

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->isNewRecord) {
            if ($this->hasAttribute('createdAt') && empty($this->createdAt)) {
                $this->createdAt = Jdf::jdate('Y-m-d H:i:s');
            }
        }
        if ($this->hasAttribute('updatedAt')) {
            $this->updatedAt = Jdf::jdate('Y-m-d H:i:s');
        }
        return true;
    }

    public function attributeLabels()
    {
        return Model::attributeLabelsList();
    }

}
