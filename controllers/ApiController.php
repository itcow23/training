<?php

namespace app\controllers;

use app\models\Account;
use Yii;
use yii\rest\Controller;

class ApiController extends Controller
{
    protected const STATUS_OK = 200;
    protected const STATUS_BAD_REQUEST = 400;
    protected const STATUS_UNAUTHORIZED = 401;
    protected const STATUS_FORBIDDEN = 403;
    protected const STATUS_NOT_FOUND = 404;
    protected const STATUS_CONFLICT = 409;
    protected const STATUS_UNPROCESSABLE_ENTITY = 422;
    protected const STATUS_INTERNAL_SERVER_ERROR = 500;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->response->statusCode = 401;
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->data = [
                'success' => false,
                'message' => 'You must be logged in to perform this action.',
            ];
            Yii::$app->response->send();
            return false;
        }
        return parent::beforeAction($action);
    }

    protected function requirePermission(string $permissionName): void
    {
        if (!Yii::$app->user->can($permissionName)) {
            throw new \yii\web\ForbiddenHttpException(
                'You do not have permission to perform this action.'
            );
        }
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH', 'POST'],
            'delete' => ['DELETE', 'POST'],
        ];
    }

    protected function success($data = null, string $message = 'Success')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    protected function error(string $message = 'Error', int $statusCode = self::STATUS_BAD_REQUEST, $errors = null)
    {
        $this->response->statusCode = $statusCode;
        $this->response->statusText = $message;

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            if ($errors instanceof \yii\base\Model) {
                $modelErrors = $errors->getErrors();
                $response['errors'] = empty($modelErrors) ? ['_form' => [$message]] : $modelErrors;
            } else {
                $response['errors'] = $errors;
            }
        }

        return $response;
    }
}
