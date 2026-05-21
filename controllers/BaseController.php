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
        if (is_object($errors) && isset($errors->errors)) {
            $errorData = $errors->errors;
        } elseif (is_array($errors)) {
            $errorData = $errors;
        } elseif (is_string($errors)) {
            $errorData = [$errors];
        }

        return [
            'success' => false,
            'message' => $message,
            'errors' => $errorData,
        ];
    }

    protected function listResponse(array $items, int $total, int $page, int $pageSize, int $pageCount, string $message = 'Success'): array
    {
        Yii::$app->response->statusCode = 200;

        return [
            'success' => true,
            'message' => $message,
            'data' => $items,
            'meta' => [
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'pageCount' => $pageCount,
                ],
            ],
        ];
    }
}
