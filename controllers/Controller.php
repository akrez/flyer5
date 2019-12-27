<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller as BaseController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class Controller extends BaseController
{

    public $successful = null;
    public $newModel = null;
    public $searchModel = null;
    public $parentModel = null;
    public $parentSearchModel = null;

    public function defaultBehaviors($rules = []) //behaviors
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => $rules,
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Url::current());
                        return $this->redirect(['/site/signin']);
                    }
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ];
    }

    public function commonIndex($id = null, $options = [])
    {
        $options = $options + [
            'manipulate' => true,
            'extraParams' => [],
            'redirectUrl' => null,
            'staticAttributes' => [],
            'view' => 'index',
        ];

        $model = null;

        if ($options['manipulate']) {
            $redirectUrl = $options['redirectUrl'];
            if ($redirectUrl === null) {
                $redirectUrl = Url::current(['id' => null]);
            }

            if ($id === null) {
                if ($this->newModel->load(Yii::$app->request->post())) {
                    $this->newModel->setAttributes($options['staticAttributes'], false);
                    if ($this->newModel->save()) {
                        $this->successful = true;
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Information has been saved successfully.'));
                        return $this->redirect($redirectUrl);
                    } else {
                        $this->successful = false;
                        $errors = $this->newModel->getErrorSummary(true);
                        Yii::$app->session->setFlash('danger', reset($errors));
                    }
                }
            } else {
                $model = $this->findModel($id);
                if ($model->load(Yii::$app->request->post())) {
                    $model->setAttributes($options['staticAttributes'], false);
                    if ($model->save()) {
                        $this->successful = true;
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Information has been updated successfully.'));
                        return $this->redirect($redirectUrl);
                    } else {
                        $this->successful = false;
                        $errors = $model->getErrorSummary(true);
                        Yii::$app->session->setFlash('danger', reset($errors));
                    }
                }
            }
        }

        $dataProvider = null;
        if ($this->searchModel) {
            $dataProvider = $this->searchModel->search(Yii::$app->request->queryParams, $this->newModel, $this->parentModel);
        }

        return $this->render($options['view'], $options['extraParams'] + [
                    'searchModel' => $this->searchModel,
                    'newModel' => $this->newModel,
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'parentModel' => $this->parentModel,
        ]);
    }

    public function commonDelete($id, $redirectUrl = null)
    {
        if ($redirectUrl === null) {
            $redirectUrl = Url::current(['id' => null]);
        }
        $this->findModel($id)->delete();
        return $this->redirect($redirectUrl);
    }

    public function commonStatus($id, $redirectUrl = null, $status = Status::STATUS_DELETED)
    {
        if ($redirectUrl === null) {
            $redirectUrl = Url::current(['id' => null]);
        }
        $model = $this->findModel($id);
        $model->status = $status;
        $model->save(false);
        return $this->redirect($redirectUrl);
    }

    public function findModel($id)
    {
        $model = $this->newModel->findOne($id);
        if ($model) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    public function findParentModel($id)
    {
        $this->parentModel = $this->parentSearchModel->findOne($id);
        if ($this->parentModel) {
            return $this->parentModel;
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

}
