<?php

namespace app\controllers;

use app\components\Helper;
use app\models\Hrm;
use app\models\HrmSearch;
use app\controllers\Controller;
use Yii;

class HrmController extends Controller
{
    public function behaviors()
    {
        return $this->defaultBehaviors([
            [
                'actions' => ['index', 'delete', 'suggest'],
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
            $model = Helper::findOrFail(Hrm::blogValidQuery($id)->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new Hrm();
        $searchModel = new HrmSearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $updateCacheNeeded = Helper::store($newModel, $post, []);
        } elseif ($state == 'update' && $model) {
            $updateCacheNeeded = Helper::store($model, $post, []);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            $newModel = new Hrm();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'model', 'dataProvider'));
    }
}
