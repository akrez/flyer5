<?php

namespace app\controllers;

use app\components\Helper;
use app\models\EntityFarvand;
use app\models\EntitySearch;
use app\models\TypeSearch;
use app\models\TypeRaw;
use app\models\TypePart;
use app\models\TypeSamane;
use app\models\TypeFarvand;
use app\models\TypeProperty;
use app\models\TypeReseller;
use Yii;

class EntityController extends Controller
{
    public function behaviors()
    {
        return $this->defaultBehaviors([
            [
                'actions' => [
                    'index-farvand', 'index-raw', 'index-samane', 'index-part', 'index-reseller', 'index-property',
                ],
                'allow' => true,
                'verbs' => ['POST', 'GET'],
                'roles' => ['@'],
            ],
        ]);
    }

    /*
    ** INDEX
    */
    public function actionIndexFarvand($id = null)
    {
        return $this->index($id, EntityFarvand::getEntityClass());
    }

    public function actionIndexRaw($id = null)
    {
        return $this->index($id, TypeRaw::getCategoryClass());
    }

    public function actionIndexSamane($id = null)
    {
        return $this->index($id, TypeSamane::getCategoryClass());
    }

    public function actionIndexPart($id = null)
    {
        return $this->index($id, TypePart::getCategoryClass());
    }

    public function actionIndexReseller($id = null)
    {
        return $this->index($id, TypeReseller::getCategoryClass());
    }

    public function actionIndexProperty($id = null)
    {
        return $this->index($id, TypeProperty::getCategoryClass());
    }

    protected function index($id, $entityClass = null)
    {
        $categoryClass = $entityClass::getCategoryClass();
        //
        $id = empty($id) ? null : intval($id);
        $post = Yii::$app->request->post();
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($id) {
            $model = Helper::findOrFail($entityClass::validQuery()->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new $entityClass();
        $searchModel = new EntitySearch();
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $entityClass);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'model', 'dataProvider'));
    }
}
