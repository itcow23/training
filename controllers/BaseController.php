<?php

namespace app\controllers;

use Yii;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

abstract class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    protected function findModelByClass(string $class, $id): Model
    {
        if (($model = $class::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function successResponse(string $message = 'success', array $data = []): array
    {
        return array_merge(['msg' => $message], $data);
    }

    protected function errorResponse($data, string $message = 'error'): array
    {
        return [
            'msg' => $message,
            'error' => is_object($data) && isset($data->errors)
                ? $data->errors
                : $data,
        ];
    }
}
