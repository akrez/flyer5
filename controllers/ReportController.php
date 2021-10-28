<?php

namespace app\controllers;

use app\models\Entity;
use app\components\Helper;

class ReportController extends Controller
{
    public function behaviors()
    {
        return $this->defaultBehaviors([
            [
                'actions' => ['index'],
                'allow' => true,
                'verbs' => ['POST', 'GET'],
                'roles' => ['@'],
            ],
        ]);
    }

    public function actionIndex($barcode)
    {
        $model = Helper::findOrFail(Entity::validQuery()->andWhere(['barcode' => $barcode]));
        //
        $rootModel = clone $model;
        while ($findModel = $rootModel->parent) {
            $rootModel = $findModel;
        }
        //
        $models = $childsMap = $parentMap = $modelsLevel = [];
        $rootModel->findChildModels($models, $childsMap, $parentMap, $modelsLevel, 1);
        //
        return $this->render('relation', [
            'model' => $model,
            'models' => $models,
            'childsMap' => $childsMap,
            'parentMap' => $parentMap,
            'modelsLevel' => $modelsLevel,
        ]);
    }
}
