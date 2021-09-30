<?php

namespace app\controllers;

use yii\helpers\Url;
use app\models\RawType;
use app\components\Helper;
use app\controllers\Controller;
use app\models\RawTypeSearch;
use Yii;

class RawtypeController extends Controller
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

    public function actionIndex($typeId, $id = null)
    {
        $categoryClass = RawType::class;
        $id = empty($id) ? null : intval($id);
        $post = Yii::$app->request->post();
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($id) {
            $model = Helper::findOrFail($categoryClass::validQuery()->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new $categoryClass();
        $searchModel = new RawTypeSearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $updateCacheNeeded = Helper::store($newModel, $post, [
                'categoryId' => $categoryClass,
            ]);
        } elseif ($state == 'update' && $model) {
            $updateCacheNeeded = Helper::store($model, $post, [
                'categoryId' => $categoryClass,
            ]);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            $newModel = new $categoryClass();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $categoryClass);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'model', 'dataProvider'));
    }

    public function actionDelete($id, $typeId)
    {
        $this->findParentModel($typeId);
        $redirectUrl = Url::to(['/rawtype/index', 'typeId' => $typeId]);
        return $this->commonDelete($id, $redirectUrl);
    }
}
