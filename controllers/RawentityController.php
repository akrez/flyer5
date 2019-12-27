<?php

namespace app\controllers;

use app\models\Entity;
use app\models\EntitySearch;
use app\models\RawEntity;
use app\models\RawEntitySearch;
use yii\helpers\Url;

class RawentityController extends Controller
{

    public function init()
    {
        parent::init();
        $this->newModel = new RawEntity();
        $this->searchModel = new RawEntitySearch();
        $this->parentModel = new Entity();
        $this->parentSearchModel = new EntitySearch();
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

    public function actionIndex($entityId, $id = null)
    {
        $this->findParentModel($entityId);
        $this->newModel->entityId = $entityId;
        return $this->commonIndex($id);
    }

    public function actionDelete($id, $typeId)
    {
        $this->findParentModel($typeId);
        $redirectUrl = Url::to(['/rawentity/index', 'typeId' => $typeId]);
        return $this->commonDelete($id, $redirectUrl);
    }

}
