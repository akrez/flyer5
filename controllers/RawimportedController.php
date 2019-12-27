<?php

namespace app\controllers;

use app\models\RawImported;
use app\models\RawImportedSearch;


class RawimportedController extends Controller
{

    public function init()
    {
        parent::init();
        $this->newModel = new RawImported();
        $this->searchModel = new RawImportedSearch();
    }

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

    public function actionIndex($id = null)
    {
        return $this->commonIndex($id);
    }

    public function actionDelete($id)
    {
        return $this->commonDelete($id);
    }

}
