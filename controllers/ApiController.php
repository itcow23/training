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


    protected function optionAuthActions()
    {
        return [];
    }

    public function beforeAction($action)
    {
        $accountId = Yii::$app->request->get('account_id');
        $optionsAuthActions = $this->optionAuthActions();

        if(in_array($action->id, $optionsAuthActions)) {
            if($accountId){
                $account = Account::findIdentity($accountId);
                if($account){
                    Yii::$app->user->login($account);
                }
            }
            return parent::beforeAction($action);
        }

        if (!$accountId) {

            Yii::$app->response->statusCode = 401;
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->data = [
                'success' => false,
                'message' => 'account_id is required',
            ];

            Yii::$app->response->send();

            return false;
        }

        $account = Account::findIdentity($accountId);
        if (!$account) {
            Yii::$app->response->statusCode = 401;
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->data = [
                'success' => false,
                'message' => 'Invalid account_id',
            ];

            Yii::$app->response->send();

            return false;
        }

        Yii::$app->user->login($account);

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
