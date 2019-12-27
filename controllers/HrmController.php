<?php

namespace app\controllers;

use app\models\Hrm;
use app\models\HrmSearch;
use Yii;

class HrmController extends Controller
{

    public function init()
    {
        parent::init();
        $this->newModel = new Hrm();
        $this->searchModel = new HrmSearch();
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

    public function actionSuggest()
    {
        $term = Yii::$app->request->get('term');
        $role = Yii::$app->request->get('role');
        $hrm = new Hrm();
        $results = [];
        $databaseResults = $hrm::find()
                        ->filterWhere(['role' => $role])
                        ->andFilterWhere([
                            'OR',
                            ['LIKE', 'fullname', $term],
                            ['LIKE', 'code', $term],
                        ])
                        ->indexBy('id')->asArray()->all();
        foreach ($databaseResults as $databaseResult) {
            $results[] = ['id' => $databaseResult['id'], 'text' => $databaseResult['fullname'] . ' (' . $databaseResult['code'] . ')'];
        }
        return $this->asJson(['results' => $results]);
    }

}
