<?php

namespace app\controllers;

use app\models\Entity;
use app\models\EntitySearch;
use app\models\RawEntity;
use app\models\RawEntitySearch;
use app\components\Helper;
use Yii;

class RawentityController extends Controller
{

    public function behaviors()
    {
        return $this->defaultBehaviors([
            [
                'actions' => ['index', 'delete'],
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
            $model = Helper::findOrFail(RawEntity::validQuery($entityBarcode, $id)->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new RawEntity();
        $searchModel = new RawEntitySearch();
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
            $newModel = new RawEntity();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $parentModel->barcode);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'parentModel', 'parentSearchModel', 'model', 'dataProvider'));
    }
}
