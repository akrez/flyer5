<?php

namespace app\controllers;

use app\models\RawType;
use app\models\RawTypeSearch;
use app\models\Type;
use app\models\TypeSearch;
use yii\helpers\Url;

class RawtypeController extends Controller
{

    public function init()
    {
        parent::init();
        $this->newModel = new RawType();
        $this->searchModel = new RawTypeSearch();
        $this->parentModel = new Type();
        $this->parentSearchModel = new TypeSearch();
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

    public function actionIndex($typeId, $id = null)
    {
        $this->findParentModel($typeId);
        $this->newModel->typeId = $typeId;
        return $this->commonIndex($id);
    }

    public function actionDelete($id, $typeId)
    {
        $this->findParentModel($typeId);
        $redirectUrl = Url::to(['/rawtype/index', 'typeId' => $typeId]);
        return $this->commonDelete($id, $redirectUrl);
    }

}
