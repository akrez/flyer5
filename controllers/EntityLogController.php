<?php

namespace app\controllers;

use app\models\Entity;
use app\models\EntitySearch;
use app\models\EntityLog;
use app\models\EntityLogSearch;
use app\components\Helper;
use Yii;

class EntityLogController extends Controller
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

    public function actionIndex($entityBarcode, $id = null)
    {
        $id = empty($id) ? null : intval($id);
        $entityBarcode = intval($entityBarcode);
        $post = Yii::$app->request->post();
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($id) {
            $model = Helper::findOrFail(EntityLog::validQuery($id)->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new EntityLog();
        $searchModel = new EntityLogSearch();
        $parentModel = Helper::findOrFail(Entity::validQuery()->andWhere(['barcode' => $entityBarcode]));
        $parentSearchModel = new EntitySearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $updateCacheNeeded = Helper::store($newModel, $post, [
                'entityBarcode' => $entityBarcode,
            ]);
        } elseif ($state == 'update' && $model) {
            $updateCacheNeeded = Helper::store($model, $post, [
                'entityBarcode' => $entityBarcode,
            ]);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            $newModel = new EntityLog();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $parentModel->barcode);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'parentModel', 'parentSearchModel', 'model', 'dataProvider'));
    }
}
