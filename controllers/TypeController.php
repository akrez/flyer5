<?php

namespace app\controllers;

use app\models\Type;
use app\components\Helper;
use app\models\TypeSearch;
use Yii;

class TypeController extends Controller
{

    public static function getCategoryId()
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

    public function actionIndex($id = null)
    {
        $newModelClass = static::getCategoryId();
        $id = empty($id) ? null : intval($id);
        $post = Yii::$app->request->post();
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($id) {
            $model = Helper::findOrFail(Type::blogValidQuery($id)->andWhere(['id' => $id]));
        } else {
            $model = null;
        }
        $newModel = new $newModelClass();
        $searchModel = new TypeSearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $updateCacheNeeded = Helper::store($newModel, $post, []);
        } elseif ($state == 'update' && $model) {
            $updateCacheNeeded = Helper::store($model, $post, []);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            $newModel = new $newModelClass();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, static::getCategoryId());
        return $this->render('..\type\index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'model', 'dataProvider'));
    }

    public function actionSuggest()
    {
        $term = Yii::$app->request->get('term');
        $results = [];
        $databaseResults = Type::find()
            ->where(['categoryId' => static::getCategoryId()])
            ->andFilterWhere([
                'OR',
                ['LIKE', 'name', $term],
                ['LIKE', 'shortname', $term],
            ])
            ->with('parent')->all();
        foreach ($databaseResults as $databaseResult) {
            $results[] = ['id' => $databaseResult->id, 'text' => $databaseResult->name . ' (' . $databaseResult->shortname . ')'];
        }
        return $this->asJson(['results' => $results]);
    }
}
