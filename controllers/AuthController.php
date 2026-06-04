<?php

namespace app\controllers;

use app\models\forms\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class AuthController extends Controller
{
    public function behaviors()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'acccess' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return ['msg' => 'Please log out of your account before logging in with a new account.'];
        }

        if (!Yii::$app->request->isPost) {
            return [
                'success' => false,
                'msg' => 'Please send POST request with email and password to log in.'
            ];
        }

        $model = new LoginForm();
        $model->load($this->request->post(), '') ;
        if ($model->login()) {
            $hasPermission =
                Yii::$app->user->can('category.view') ||
                Yii::$app->user->can('product.view') ||
                Yii::$app->user->can('post.view') ||
                Yii::$app->user->can('order.view');

            if ($hasPermission) {
                return ['msg' => 'Login admin successfully'];
            } else {
                return ['msg' => 'Login client successfully'];
            }
        }

        return [
            'success' => false,
            'msg' => 'Login fail',
            'errors' => $model->getErrors()
        ];
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return ['msg' => 'Logout successfully'];
    }
}
