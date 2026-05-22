<?php

namespace app\controllers;

use app\models\response\CategoryResponse;
use app\models\response\OrderResponse;
use app\models\response\PostCategoryResponse;
use app\models\response\PostResponse;
use app\models\response\ProductResponse;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

abstract class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    private const MODEL_DEFAULT_RELATIONS = [
        OrderResponse::class => ['orderItems.product'],
        PostResponse::class => ['comments', 'ratings', 'media'],
        ProductResponse::class => ['category', 'media'],
        CategoryResponse::class => ['products', 'media'],
        PostCategoryResponse::class => ['posts'],
    ];

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }


    public function behaviors()
    {
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


    protected function findModelByClass(string $class, $id, ?array $with = null): Model
    {
        if (!is_subclass_of($class, ActiveRecord::class, true)) {
            throw new \InvalidArgumentException('findModelByClass expects an ActiveRecord class name.');
        }

        $arClass = $class;
        $query = $arClass::find()->where(['id' => $id]);

        $relations = $with ?? (self::MODEL_DEFAULT_RELATIONS[$arClass] ?? []);
        if ($relations !== []) {
            $query->with($relations);
        }

        if (($model = $query->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function successResponse(array $data = [], string $message = 'Success', int $statusCode = 200): array
    {
        Yii::$app->response->statusCode = $statusCode;

        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    protected function errorResponse($errors, string $message = 'Error', int $statusCode = 400): array
    {
        Yii::$app->response->statusCode = $statusCode;

        $errorData = [];
        if ($errors instanceof Model) {
            $errorData = $errors->getErrors();
        } elseif (is_object($errors) && isset($errors->errors)) {
            $errorData = $errors->errors;
        } elseif (is_array($errors)) {
            $errorData = $errors;
        } else {
            $errorData = (array) $errors;
        }

        $finalErrors = $errorData;
        if (empty($errorData)) {
            $finalErrors = [$message];
        }

        return [
            'success' => false,
            'message' => $message,
            'errors' => $finalErrors,
        ];
    }

    /**
     * Responds with errors from one or more models (e.g. Form, Active Record).
     *
     * @param Model[]|Model $models
     * @param string $message
     * @param int $statusCode
     * @return array
     */
    protected function modelErrorResponse($models, string $message = 'Validation failed', int $statusCode = 422): array
    {
        $errorData = [];
        
        $modelsArray = [];
        if (is_array($models)) {
            $modelsArray = $models;
        } else {
            $modelsArray = [$models];
        }

        foreach ($modelsArray as $model) {
            if ($model instanceof Model && $model->hasErrors()) {
                $errorData = array_merge($errorData, $model->getErrors());
            }
        }

        $finalErrors = $errorData;
        if (empty($errorData)) {
            $finalErrors = ['message' => [$message]];
        }

        return $this->errorResponse($finalErrors, $message, $statusCode);
    }

    /**
     * Formats an ActiveDataProvider response with automatic pagination extraction.
     *
     * @param \yii\data\ActiveDataProvider $dataProvider
     * @param string $message
     * @return array
     */
    protected function dataProviderResponse(\yii\data\ActiveDataProvider $dataProvider, string $message = 'Success'): array
    {
        $models = $dataProvider->getModels();
        $totalCount = (int) $dataProvider->getTotalCount();
        
        $pagination = $dataProvider->getPagination();
        
        $page = 1;
        $pageSize = count($models);
        
        $pageCount = 1;
        if ($totalCount === 0) {
            $pageCount = 0;
        }

        if ($pagination !== null && $pagination !== false) {
            $page = $pagination->getPage() + 1;
            $pageSize = $pagination->getPageSize();
            $pageCount = $pagination->getPageCount();
        }

        Yii::$app->response->statusCode = 200;

        return [
            'success' => true,
            'message' => $message,
            'data' => $models,
            'meta' => [
                'pagination' => [
                    'total' => $totalCount,
                    'page' => (int) $page,
                    'pageSize' => (int) $pageSize,
                    'pageCount' => (int) $pageCount,
                ],
            ],
        ];
    }
}
