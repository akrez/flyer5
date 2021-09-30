<?php

namespace app\controllers;

use app\models\Type;
use yii\helpers\Url;
use app\models\RawType;
use app\components\Helper;
use app\models\TypeSearch;
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
        $id = empty($id) ? null : intval($id);
        $typeId = intval($typeId);
        $post = Yii::$app->request->post();
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($id) {
            $model = Helper::findOrFail(RawType::validQuery($typeId, $id)->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new RawType();
        $searchModel = new RawTypeSearch();
        $parentModel = Helper::findOrFail(Type::validQuery()->andWhere(['id' => $typeId]));
        $parentSearchModel = new TypeSearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $updateCacheNeeded = Helper::store($newModel, $post, [
                'typeId' => $typeId,
            ]);
        } elseif ($state == 'update' && $model) {
            $oldStatus = $model->status;
            $updateCacheNeeded = Helper::store($model, $post, [
                'typeId' => $typeId,
            ]);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            $newModel = new RawType();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $parentModel);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'parentModel', 'parentSearchModel', 'model', 'dataProvider'));
    }
}
