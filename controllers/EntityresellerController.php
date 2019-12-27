<?php

namespace app\controllers;

use app\models\Entity;
use app\models\EntitySearch;
use app\models\Type;
use Yii;
use yii\helpers\Url;

class EntityresellerController extends Controller
{

    public static $category = Type::CATEGORY_RESELLER;

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
        $handlerModel = new Entity([
            'categoryId' => self::$category,
        ]);
        $actionCOnfig = [
            'view' => '/entity/index',
            'manipulate' => true,
            'extraParams' => [
                'categoryId' => self::$category,
            ],
            'staticAttributes' => [
                'categoryId' => self::$category,
            ],
        ];
        /////
        if ($handlerModel->load(Yii::$app->request->post()) == false) {
            $this->newModel->id = Entity::suggestId(self::$category);
            return $this->commonIndex($id, $actionCOnfig);
        }
        /////
        if ($id != null) {
            $this->newModel->id = Entity::suggestId(self::$category);
            return $this->commonIndex($id, $actionCOnfig);
        }
        /////
        if ($handlerModel->_count < 2) {
            $this->newModel->id = Entity::suggestId(self::$category);
            return $this->commonIndex($id, $actionCOnfig);
        }
        /////
        if ($handlerModel->validate() == false) {
            $this->newModel->id = Entity::suggestId(self::$category);
            return $this->commonIndex($id, $actionCOnfig);
        }
        /////
        $actionCOnfig['manipulate'] = false;
        $ids = [];
        for ($i = 0; $i < $handlerModel->_count; $i++) {
            $ids[] = $handlerModel->id + $i;
        }
        $duplicateIds = Entity::find()->select('id')->where(['id' => $ids])->column();
        if ($duplicateIds) {
            Yii::$app->session->setFlash('danger', 'این بارکدها تکراری هستند: ' . implode(' , ', $duplicateIds));
            return $this->commonIndex($id, $actionCOnfig);
        }
        /////
        $transaction = Yii::$app->db->beginTransaction();
        if (Entity::resellerBatchInsert($handlerModel, $ids)) {
            $transaction->commit();
            $redirectUrl = Url::current(['id' => null]);
            return $this->redirect($redirectUrl);
        } else {
            $transaction->rollBack();
            return $this->commonIndex($id, $actionCOnfig);
        }
    }

    public function actionDelete($id)
    {
        return $this->commonDelete($id);
    }

}
