<?php

namespace app\controllers;

use app\models\Type;
use app\models\TypeSearch;
use Yii;

class TypefarvandController extends Controller
{

    public static $category = Type::CATEGORY_FARVAND;

    public function init()
    {
        parent::init();
        $this->newModel = new Type([
            'categoryId' => self::$category,
        ]);
        $this->searchModel = new TypeSearch([
            'categoryId' => self::$category,
        ]);
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
        return $this->commonIndex($id, [
                    'view' => '/type/index',
                    'extraParams' => [
                        'categoryId' => self::$category,
                    ],
                    'staticAttributes' => [
                        'categoryId' => self::$category,
                    ],
        ]);
    }

    public function actionDelete($id)
    {
        return $this->commonDelete($id);
    }

    public function actionSuggest()
    {
        $term = Yii::$app->request->get('term');
        $results = [];
        $databaseResults = Type::find()
                        ->where(['categoryId' => Type::CATEGORY_FARVAND])
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
