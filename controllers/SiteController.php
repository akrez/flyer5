<?php

namespace app\controllers;

use app\models\Status;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;

class SiteController extends Controller
{

    public function behaviors()
    {
        return $this->defaultBehaviors([
                    [
                        'actions' => ['error', 'index', 'sidebar',],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['signin', 'signup', 'reset-password-request', 'reset-password'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['signout',],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['@'],
                    ],
        ]);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $user = new User ();
        $user->validate();
        return $this->render('index');
    }

    public function actionSignin()
    {
        try {
            $signin = new User(['scenario' => 'signin']);
            if ($signin->load(Yii::$app->request->post()) && $signin->validate()) {
                Yii::$app->user->login($signin->getUser());
                return $this->goBack();
            }
            return $this->render('signin', ['model' => $signin]);
        } catch (Exception $e) {
            die($e->getMessage());
            throw new BadRequestHttpException();
        }
    }

    public function actionSignout()
    {
        try {
            $signout = Yii::$app->user->getIdentity();
            $signout->setAuthKey();
            if ($signout->save(false)) {
                Yii::$app->user->logout();
            }
            return $this->goHome();
        } catch (Exception $e) {
            throw new BadRequestHttpException();
        }
    }

    public function actionSignup()
    {
        try {
            $signup = new User(['scenario' => 'signup']);
            if ($signup->load(\Yii::$app->request->post())) {
                $signup->status = Status::STATUS_UNVERIFIED;
                $signup->setAuthKey();
                $signup->setPasswordHash($signup->password);
                if ($signup->save()) {
                    Yii::$app->user->login($signup);
                    return $this->goBack();
                }
            }
            return $this->render('signup', ['model' => $signup]);
        } catch (Exception $e) {
            throw new BadRequestHttpException();
        }
    }

    public function actionSidebar()
    {
        $sidebar = true;
        if (Yii::$app->session->get('__sidebar', false)) {
            $sidebar = false;
        }
        Yii::$app->session->set('__sidebar', $sidebar);
        return $this->asJson(['result' => $sidebar]);
    }

}
