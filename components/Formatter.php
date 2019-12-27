<?php

namespace app\components;

class Formatter extends \yii\i18n\Formatter
{

    public $datetimefa = 'Y-m-d H:i';

    public function asDatetimefa($value)
    {
        if (!is_numeric($value) && $stt = strtotime($value)) {
            $value = $stt;
        }
        if ($value) {
            return Jdf::jdate($this->datetimefa, $value);
        }
        return $this->nullDisplay;
    }

}
