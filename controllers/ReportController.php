<?php

namespace app\controllers;

use app\models\Entity;
use app\components\Helper;
use app\models\EntityLog;
use app\models\EntityLogSearch;
use app\models\EntitySearch;
use app\models\RawEntitySearch;
use Yii;

class ReportController extends Controller
{
    public function behaviors()
    {
        return $this->defaultBehaviors([
            [
                'actions' => ['index', 'entity',],
                'allow' => true,
                'verbs' => ['POST', 'GET'],
                'roles' => ['@'],
            ],
        ]);
    }

    public function actionEntity()
    {
        $entitySearch = new EntitySearch();
        $entitySearchProvider = $entitySearch->search(Yii::$app->request->queryParams, null, 10000);
        $entitySearchProvider->query->orderBy(['createdAt' => SORT_DESC,]);
        //
        return $this->render('entity', [
            'entitySearch' => $entitySearch,
            'entitySearchProvider' => $entitySearchProvider,

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
        $rawEntitySearch = new RawEntitySearch();
        $rawEntitySearchDataProvider = $rawEntitySearch->search([], $models ? array_keys($models) : [-1]);
        $rawEntitySearchDataProvider->query->orderBy(['entityBarcode' => SORT_ASC,]);
        $rawEntitySearchDataProvider->sort = false;
        $rawEntitySearchDataProvider->pagination = false;
        //
        $entityLogSearch = new EntityLogSearch();
        $entityLogSearchDataProvider = $entityLogSearch->search([], $models ? array_keys($models) : [-1]);
        $entityLogSearchDataProvider->query->orderBy(['createdAt' => SORT_ASC,])->orderBy(['entityBarcode' => SORT_ASC,]);
        $entityLogSearchDataProvider->sort = false;
        $entityLogSearchDataProvider->pagination = false;
        //
        return $this->render('index', [
            'model' => $model,
            'models' => $models,
            'childsMap' => $childsMap,
            'parentMap' => $parentMap,
            'modelsLevel' => $modelsLevel,
            //
            'rawEntitySearch' => $rawEntitySearch,
            'rawEntitySearchDataProvider' => $rawEntitySearchDataProvider,
            //
            'entityLogSearch' => $entityLogSearch,
            'entityLogSearchDataProvider' => $entityLogSearchDataProvider,

        ]);
    }
}
