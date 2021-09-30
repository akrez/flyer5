<?php

namespace app\controllers;

use app\components\Helper;
use app\models\RawImported;
use app\models\RawImportedSearch;
use Yii;

class RawimportedController extends Controller
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

    public function actionIndex($id = null)
    {
        $id = empty($id) ? null : intval($id);
        $post = Yii::$app->request->post();
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($id) {
            $model = Helper::findOrFail(RawImported::validQuery()->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new RawImported();
        $searchModel = new RawImportedSearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $updateCacheNeeded = Helper::store($newModel, $post, []);
        } elseif ($state == 'update' && $model) {
            $updateCacheNeeded = Helper::store($model, $post, []);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            $newModel = new RawImported();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'model', 'dataProvider'));
    }

    public function actionDelete($id)
    {
        return $this->commonDelete($id);
    }
}
