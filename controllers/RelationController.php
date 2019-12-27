<?php

namespace app\controllers;

use app\models\Entity;
use app\models\EntitySearch;
use app\models\Relation;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

class RelationController extends Controller
{

    public function init()
    {
        parent::init();
        $this->newModel = new Relation();
        $this->searchModel = null;
        $this->parentSearchModel = new EntitySearch();
    }

    public function behaviors()
    {
        return $this->defaultBehaviors([
                    [
                        'actions' => ['index', 'delete', 'chart'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['@'],
                    ],
        ]);
    }

    public function actionIndex($parentId)
    {
        $this->findParentModel($parentId);
        if ($this->newModel->load(Yii::$app->request->post()) && $this->newModel->validate()) {
            $this->newModel->_entity->parentId = $parentId;
            if ($this->newModel->_entity->save()) {
                $this->newModel = new Relation();
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => Entity::find()->where(['parentId' => $parentId])->with('parent')->with('type')->with('provider')->with('seller'),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC,]],
            'pagination' => false
        ]);
        return $this->render('index', [
                    'relation' => $this->newModel,
                    'model' => $this->parentModel,
                    'parentModel' => ($this->parentModel->parentId ? Entity::findOne($this->parentModel->parentId) : null),
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findParentModel($id);
        $parentId = $this->parentModel->parentId;
        $this->parentModel->parentId = null;
        $this->parentModel->save();
        return $this->redirect(['/relation/index', 'parentId' => $parentId]);
    }

    public function actionChart($parentId)
    {
        $this->findParentModel($parentId);
        //
        $rootModel = $this->parentModel;
        while ($findModel = $rootModel->parent) {
            $rootModel = $findModel;
        }
        //
        $models = $childsMap = $parentMap = [];
        $rootModel->findChildModels($models, $childsMap, $parentMap);
        //
        $dataProvider = new ActiveDataProvider([
            'query' => Entity::find()->where(['id' => array_keys($parentMap)])->with('parent')->with('type')->with('provider')->with('seller'),
            'sort' => false,
            'pagination' => false
        ]);
        //
        return $this->render('chart', [
                    'dataProvider' => $dataProvider,
                    'childsMap' => $childsMap,
                    'parentMap' => $parentMap,
                    'parentModel' => $this->parentModel,
                    'rootModel' => $rootModel,
                    'models' => $models,
        ]);
    }

}
