<?php

namespace app\controllers;

use app\models\Raw;
use app\models\RawSearch;

class RawController extends Controller
{

    public function init()
    {
        parent::init();
        $this->newModel = new Raw();
        $this->searchModel = new RawSearch();
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
        return $this->commonIndex($id);
    }

    public function actionDelete($id)
    {
        return $this->commonDelete($id);
    }

    public function actionSuggest($term = '')
    {
        $raw = new Raw();
        $results = [];
        $databaseResults = $raw::find()
                        ->filterWhere([
                            'OR',
                            ['LIKE', 'name', $term],
                            ['LIKE', 'shortname', $term],
                        ])
                        ->indexBy('id')->asArray()->all();
        foreach ($databaseResults as $databaseResult) {
            $results[] = ['id' => $databaseResult['id'], 'text' => $databaseResult['name'] . ' (' . $databaseResult['unit']. ')'];
        }
        return $this->asJson(['results' => $results]);
    }

}
