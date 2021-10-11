<?php

namespace app\controllers;

use app\components\Helper;
use app\models\TypeSearch;
use app\models\TypeRaw;
use app\models\TypePart;
use app\models\TypeSamane;
use app\models\TypeFarvand;
use app\models\TypeProperty;
use app\models\TypeReseller;
use Yii;

class TypeController extends Controller
{
    public function behaviors()
    {
        return $this->defaultBehaviors([
            [
                'actions' => [
                    'index-farvand', 'index-raw', 'index-samane', 'index-part', 'index-reseller', 'index-property',
                    'suggest-farvand', 'suggest-raw', 'suggest-samane', 'suggest-part', 'suggest-reseller', 'suggest-property',
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
        return $this->index($id, TypeFarvand::getCategoryClass());
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

    /*
    ** SUGGEST
    */
    public function actionSuggestFarvand()
    {
        return $this->suggest(TypeFarvand::getCategoryClass());
    }

    public function actionSuggestRaw()
    {
        return $this->suggest(TypeRaw::getCategoryClass());
    }

    public function actionSuggestSamane()
    {
        return $this->suggest(TypeSamane::getCategoryClass());
    }

    public function actionSuggestPart()
    {
        return $this->suggest(TypePart::getCategoryClass());
    }

    public function actionSuggestReseller()
    {
        return $this->suggest(TypeReseller::getCategoryClass());
    }

    public function actionSuggestProperty()
    {
        return $this->suggest(TypeProperty::getCategoryClass());
    }

    protected function index($id, $categoryClass = null)
    {
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
        $searchModel = new TypeSearch();
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

    protected function suggest($categoryClass = null)
    {
        $term = Yii::$app->request->get('term');
        $results = [];
        $databaseResults = $categoryClass::find()
            ->where(['categoryId' => $categoryClass])
            ->andFilterWhere([
                'OR',
                ['LIKE', 'name', $term],
                ['LIKE', 'shortname', $term],
            ])
            ->with('parent')->all();
        foreach ($databaseResults as $databaseResult) {
            $results[] = ['id' => $databaseResult->id, 'text' => $databaseResult->printNameAndShortname()];
        }
        return $this->asJson(['results' => $results]);
    }
}
