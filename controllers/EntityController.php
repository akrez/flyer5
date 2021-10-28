<?php

namespace app\controllers;

use app\components\Helper;
use app\models\Entity;
use app\models\EntityFarvand;
use app\models\EntityLog;
use app\models\EntitySearch;
use app\models\EntityPart;
use app\models\EntityProperty;
use app\models\EntityRaw;
use app\models\EntityReseller;
use app\models\EntitySamane;
use app\models\TypeRaw;
use Yii;

class EntityController extends Controller
{
    public static $entityLogAttributes = ['qc', 'qa', 'place',];

    public function behaviors()
    {
        return $this->defaultBehaviors([
            [
                'actions' => [
                    'index-farvand', 'index-raw', 'index-samane', 'index-part', 'index-reseller', 'index-property',
                    'suggest',
                ],
                'allow' => true,
                'verbs' => ['POST', 'GET'],
                'roles' => ['@'],
            ],
        ]);
    }

    public function actionSuggest()
    {
        $barcode = Yii::$app->request->get('term');
        $results = [];
        $databaseResults = Entity::validQuery()
            ->where(['<>', 'typeId', TypeRaw::getCategoryClass()])
            ->andFilterWhere(['LIKE', 'barcode', $barcode])
            ->with('parent')
            ->all();
        foreach ($databaseResults as $databaseResult) {
            $results[] = ['id' => $databaseResult->barcode, 'text' => $databaseResult->barcode];
        }
        return $this->asJson(['results' => $results]);
    }

    /*
    ** INDEX
    */
    public function actionIndexFarvand($barcode = null)
    {
        return $this->index($barcode, EntityFarvand::getEntityClass());
    }

    public function actionIndexSamane($barcode = null)
    {
        return $this->index($barcode, EntitySamane::getEntityClass());
    }

    public function actionIndexPart($barcode = null)
    {
        return $this->index($barcode, EntityPart::getEntityClass());
    }

    public function actionIndexReseller($barcode = null)
    {
        return $this->index($barcode, EntityReseller::getEntityClass());
    }

    public function actionIndexProperty($barcode = null)
    {
        return $this->index($barcode, EntityProperty::getEntityClass());
    }

    public function actionIndexRaw($barcode = null)
    {
        return $this->index($barcode, EntityRaw::getEntityClass());
    }

    protected function index($barcode, $entityClass = null)
    {
        $categoryClass = $entityClass::getCategoryClass();
        //
        $barcode = empty($barcode) ? null : intval($barcode);
        $post = Yii::$app->request->post();
        $pagesize = (int)Yii::$app->getRequest()->get('per-page', 5);
        $state = Yii::$app->request->get('state', '');
        $updateCacheNeeded = null;
        //
        if ($barcode) {
            $model = Helper::findOrFail($entityClass::validQuery()->andWhere(['barcode' => $barcode]));
            $modelOld = clone $model;
        } else {
            $model = null;
        }
        $newModel = new $entityClass();
        $searchModel = new EntitySearch();
        //
        if ($state == 'save' && $newModel->load($post)) {
            $barcodes = [];
            for ($i = 0; $i < $newModel->count; $i++) {
                $barcodes[] = $newModel->barcode + $i;
            }
            $duplicateBarcodes = Entity::validQuery()->select('barcode')->where(['barcode' => $barcodes])->column();
            if ($duplicateBarcodes) {
                Yii::$app->session->setFlash('danger', 'این بارکدها تکراری هستند: ' . implode(' , ', $duplicateBarcodes));
            } else {
                $updateCacheNeeded = Entity::batchInsert($newModel, $barcodes);
            }
        } elseif ($state == 'update' && $model) {
            $updateCacheNeeded = Helper::store($model, $post, [
                'categoryId' => $categoryClass,
            ]);
        } elseif ($state == 'remove' && $model) {
            $updateCacheNeeded = Helper::delete($model);
        }
        if ($updateCacheNeeded) {
            if ($state == 'update') {
                foreach (self::$entityLogAttributes as $logAttribute) {
                    EntityLog::log($logAttribute, $model, $modelOld);
                }
            } elseif ($state == 'save') {
                $newModel = new $entityClass();
            }
            $newModel = new $entityClass();
        }
        //
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $entityClass, $pagesize);
        return $this->render('index', [
            'state' => $state,
        ] + compact('newModel', 'searchModel', 'model', 'dataProvider'));
    }
}
