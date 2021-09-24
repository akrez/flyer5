<?php

namespace app\components;

use yii\base\Component;

class Storage extends Component
{

    public static $storage = [];

    public static function set($type, $key, $value)
    {
        return self::$storage[$type][$key] = $value;
    }

    public static function get($type, $key, $default = null)
    {
        if (self::has($type, $key)) {
            return self::$storage[$type][$key];
        }
        return $default;
    }

    public static function has($type, $key)
    {
        if (isset(self::$storage[$type][$key])) {
            return true;
        }
        return false;
    }
}
