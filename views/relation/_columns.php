<?php

use app\models\RawEntity;
use app\models\Relation;
use app\models\Type;
use yii\helpers\Html;
use yii\helpers\Url;

return [
    'id',
    'categoryId' => [
        'attribute' => 'categoryId',
        'value' => function ($data) {
            return Type::printCategory($data->categoryId);
        }
    ],
    'typeId' => [
        'attribute' => 'typeId',
        'value' => function ($data) {
            if ($data->typeId) {
                if (Yii::$app->storage::has('type', $data->typeId)) {
                    return Yii::$app->storage::get('type', $data->typeId)->name;
                }
                if ($type = $data->type) {
                    return Yii::$app->storage::set('type', $data->typeId, $type)->name;
                }
            }
            return null;
        }
    ],
    'qa' => [
        'attribute' => 'qa',
        'format' => 'html',
        'value' => function ($data) {
            if ($data->qa == 1)
                return '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
            return '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
        }
    ],
    'qc' => [
        'attribute' => 'qc',
        'format' => 'html',
        'value' => function ($data) {
            if ($data->qc == 1)
                return '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
            return '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
        }
    ],
    'factor',
    'price' => [
        'attribute' => 'price',
        'value' => function ($data) {
            if ($data->price)
                return number_format($data->price, 0);
            return '';
        }
    ],
    'submitAt',
    'productAt',
    'factorAt',
    'providerId' => [
        'attribute' => 'providerId',
        'value' => function ($data) {
            if ($data->providerId) {
                if (Yii::$app->storage::has('hrm', $data->providerId)) {
                    return Yii::$app->storage::get('hrm', $data->providerId)->fullname;
                }
                if ($provider = $data->provider) {
                    return Yii::$app->storage::set('hrm', $data->providerId, $provider)->fullname;
                }
            }
            return null;
        }
    ],
    'sellerId' => [
        'attribute' => 'sellerId',
        'value' => function ($data) {
            if ($data->sellerId) {
                if (Yii::$app->storage::has('hrm', $data->sellerId)) {
                    return Yii::$app->storage::get('hrm', $data->sellerId)->fullname;
                }
                if ($seller = $data->seller) {
                    return Yii::$app->storage::set('hrm', $data->sellerId, $seller)->fullname;
                }
            }
            return null;
        }
    ],
    'des',
    'Remove' => [
        'format' => 'raw',
        'contentOptions' => ["style" => "padding: 0px 4px;vertical-align: middle;"],
        'value' => function ($model) {
            return Html::a(' <span class="glyphicon glyphicon-trash"></span> ' . Yii::t('app', 'Remove'), Url::current([0 => 'relation/delete', 'id' => $model->id]), [
                        'class' => 'btn btn-danger btn-block btn-social btn-sm',
                        'data-pjax' => '',
            ]);
        },
        'visible' => in_array('Remove', $visible),
    ],
    'Relation' => [
        'format' => 'raw',
        'contentOptions' => ["style" => "padding: 0px 4px;vertical-align: middle;"],
        'value' => function ($model) {
            return Html::a(' <span class="glyphicon glyphicon-list-alt"></span> ' . Relation::modelName(), Url::toRoute(['/relation/index', 'parentId' => $model->id]), ['class' => 'btn btn-default btn-block btn-social btn-sm']);
        },
        'visible' => in_array('Relation', $visible),
    ],
    'RawEntity' => [
        'format' => 'raw',
        'contentOptions' => ["style" => "padding: 0px 4px;vertical-align: middle;"],
        'value' => function ($model) {
            return Html::a(' <span class="glyphicon glyphicon-oil"></span> ' . RawEntity::modelName(), Url::toRoute(['/rawentity/index', 'entityId' => $model->id]), ['class' => 'btn btn-default btn-block btn-social btn-sm']);
        },
        'visible' => in_array('RawEntity', $visible),
    ],
];
