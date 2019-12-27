<?php

namespace app\controllers;

use app\models\Entity;
use app\models\EntitySearch;
use app\models\Type;

class EntitysamaneController extends Controller
{

    public static $category = Type::CATEGORY_SAMANE;

    public function init()
    {
        parent::init();
        $this->newModel = new Entity([
            'categoryId' => self::$category,
        ]);
        $this->searchModel = new EntitySearch([
            'categoryId' => self::$category,
        ]);
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
        $this->newModel->id = Entity::suggestId(self::$category);
        return $this->commonIndex($id, [
                    'view' => '/entity/index',
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

}
