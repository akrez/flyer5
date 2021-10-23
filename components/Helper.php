<?php

namespace app\components;

use Yii;
use app\components\Jdf;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\web\JsExpression;
use yii\web\NotFoundHttpException;

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
        $input = preg_split('/\D/', $input, -1, PREG_SPLIT_NO_EMPTY);
        if (count($input) >= 3 && Jdf::jcheckdate($input[1], $input[2], $input[0])) {
            $time = Jdf::jmktime(0, 0, 0, $input[1], $input[2], $input[0]);
            return Jdf::jdate($format, $time);
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
            "0000000000"
        ];

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

    public static function store(&$newModel, $post, $staticAttributes = [], $setFlash = true)
    {
        if (!$newModel->load($post)) {
            return null;
        }
        //
        $newModel->setAttributes($staticAttributes, false);
        if ($newModel->hasAttribute('parentBarcode') && !mb_strlen($newModel->parentBarcode)) {
            $newModel->parentBarcode = null;
        }
        $isNewRecord = $newModel->isNewRecord;
        $isSuccessful = $newModel->save();
        //
        if (!$setFlash) {
            return $isSuccessful;
        }
        //
        if ($isSuccessful) {
            if ($isNewRecord) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'alertAddSuccessfull'));
            } else {
                Yii::$app->session->setFlash('success', Yii::t('app', 'alertUpdateSuccessfull'));
            }
        } else {
            $errors = $newModel->getErrorSummary(true);
            Yii::$app->session->setFlash('danger', reset($errors));
        }
        //
        return $isSuccessful;
    }

    public static function delete(&$model, $setFlash = true)
    {
        $isSuccessful = $model->delete();
        if ($setFlash) {
            if ($isSuccessful) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'alertRemoveSuccessfull'));
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'alertRemoveUnSuccessfull'));
            }
        }
        return $isSuccessful;
    }

    public static function findOrFail(ActiveQuery $query)
    {
        $model = $query->one();
        if ($model) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    public static function getSelect2FieldConfig($model, $attribute, $url, $options = [])
    {
        $options = $options + [
            'data' => [],
            'placeholder' => '',
            'id' => 'id-' . rand(10000, 99999),
        ];
        return [
            'model' => $model,
            'attribute' => $attribute,
            'data' => $options['data'],
            'options' => [
                'placeholder' => $options['placeholder'],
                'id' => $options['id'],
                'dir' => 'rtl',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params) { return {term:params.term, page: params.page}; }'),
                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                ]
            ],
        ];
    }
}
