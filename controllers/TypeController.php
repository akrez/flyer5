<?php

namespace app\controllers;

use app\models\Type;
use app\components\Helper;
use app\models\TypeSearch;
use Yii;

class TypeController extends Controller
{

    public static function getCategoryClass()
    {
        return Type::class;
    }

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

    protected function index($id = null)
    {
        $newCategoryModel = static::getCategoryClass();
        $id = empty($id) ? null : intval($id);
        $post = Yii::$app->request->post();
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($id) {
            $model = Helper::findOrFail($newCategoryModel::validQuery()->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new $newCategoryModel();
        $searchModel = new TypeSearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $updateCacheNeeded = Helper::store($newModel, $post, [
                'categoryId' => $newCategoryModel,
            ]);
        } elseif ($state == 'update' && $model) {
            $updateCacheNeeded = Helper::store($model, $post, [
                'categoryId' => $newCategoryModel,
            ]);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            $newModel = new $newCategoryModel();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $newModel);
        return $this->render('..\type\index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'model', 'dataProvider'));
    }

    protected function suggest()
    {
        $modelCategory = static::getCategoryClass();
        $term = Yii::$app->request->get('term');
        $results = [];
        $databaseResults = $modelCategory::find()
            ->where(['categoryId' => static::getCategoryClass()])
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
