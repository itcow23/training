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
        OrderResponse::class => ['orderDetails.product'],
        PostResponse::class => ['comments', 'ratings', 'media'],
        ProductResponse::class => ['category', 'media'],
        CategoryResponse::class => ['products', 'media'],
        PostCategoryResponse::class => ['posts'],
    ];

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
