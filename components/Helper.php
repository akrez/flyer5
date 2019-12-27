<?php

namespace app\components;

use yii\base\Component;

class Helper extends Component
{

    public static function normalizeArray($arr, $arrayOut = false)
    {
        if (is_array($arr)) {
            $arr = implode(",", $arr);
        }
        $arr = str_ireplace("\n", ",", $arr);
        $arr = str_ireplace(",", ",", $arr);
        $arr = str_ireplace("،", ",", $arr);
        $arr = explode(",", $arr);
        $arr = array_map("trim", $arr);
        $arr = array_unique($arr);
        $arr = array_filter($arr);
        sort($arr);
        if ($arrayOut) {
            return $arr;
        }
        return implode(",", $arr);
    }

    public static function formatDate($input, $format = 'Y-m-d')
    {
        $input = preg_split('/\D/', $input, NULL, PREG_SPLIT_NO_EMPTY);
        if (count($input) == 3 && jdf::jcheckdate($input[1], $input[2], $input[0])) {
            $time = jdf::jmktime(0, 0, 0, $input[1], $input[2], $input[0]);
            return jdf::jdate($format, $time);
        }
        return null;
    }

    public static function validateNationalCode($NationalCode)
    {
        $NationalCode = preg_replace("/[^0-9]/", '', $NationalCode);
        $notNationalCode = [
            "1111111111",
            "2222222222",
            "3333333333",
            "4444444444",
            "5555555555",
            "6666666666",
            "7777777777",
            "8888888888",
            "9999999999",
            "0000000000"];

        if (in_array($NationalCode, $notNationalCode)) {
            
        } else {

            if (
                    (is_numeric($NationalCode)) &&
                    (strlen($NationalCode) == 10) &&
                    (strspn($NationalCode, $NationalCode[0]) != strlen($NationalCode))
            ) {
                $subMid = substr($NationalCode, (10 - 1), 1);
                $getNum = 0;
                for ($i = 1; $i < 10; $i++) {
                    $getNum += (substr($NationalCode, ($i - 1), 1) * (11 - $i));
                }
                $modulus = ($getNum % 11);
                if (
                        (($modulus < 2) && ($subMid == $modulus)) ||
                        (($modulus >= 2) && ($subMid == (11 - $modulus)))
                ) {
                    return true;
                }
            }
        }
        return false;
    }

}
