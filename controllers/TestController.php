<?php

namespace app\controllers;

use app\models\Product;
use app\models\response\CategoryResponse;
use Override;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class TestController extends Controller
{
    public $enableCsrfValidation = false;

    #[Override]
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }


    public function actionProductStatus($status, $page = 1, $pageSize = 10)
    {

        $offset = ($page - 1) * $pageSize;
        $product = Product::find()
            ->select(['product.id', 'product.name', 'product.price', 'category.name AS category_name'])
            ->joinWith('category', false, 'INNER JOIN')
            ->where(['status' => $status])
            ->offset($offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        if (empty($product)) {
            return [
                'code' => 404,
                'message' => 'No products found with the specified status',
                'data' => null
            ];
        }

        return [
            'code' => 200,
            'message' => 'success',
            'data' => [
                'product' => $product,
                'total' => count($product),
                'page' => $page,
                'pageSize' => $pageSize,
            ],
        ];
    }

    public function actionDataProduct($slug)
    {

        $data = Product::find()
            ->select([
                'product.name as product_name',
                'category.name as category_name',
                'media.filepath as media_filepath',
                'post.title as post_title',
            ])
            ->joinWith(['category', 'postProducts',], false, 'LEFT JOIN')
            ->leftJoin('post', 'post.id = post_product.post_id')
            ->leftJoin('media', 'media.file_id = product.id AND media.file_type = "product"')
            // ->with('category','media','post')
            ->where(['product.slug' => $slug])
            ->asArray()
            ->all();

        if (empty($data)) {
            return [
                'code' => 404,
                'message' => 'Product not found',
                'data' => null
            ];
        }
        return [
            'code' => 200,
            'message' => 'success',
            'data' => $data
        ];
    }

    public function actionProductCategory()
    {

        $category = CategoryResponse::find()
            ->joinWith('products', false, 'INNER JOIN')
            ->where(['product.status' => 1])
            ->with([
                'products' => function ($query) {
                    $query->where(['status' => 1])
                        ->select(['id', 'name', 'price']);

                }
            ])
            ->all();

        //     $data = [];
        // foreach ($category as $item) {

        //     $products = array_slice($item->products, 0, 2);
        //     $data[] = [
        //         'category' => $item,
        //         'products' => $products
        //     ];
        //}

        if (empty($category)) {
            return [
                'code' => 404,
                'message' => 'Category not found',
                'data' => null
            ];
        }


        return $this->asJson([
            'code' => 200,
            'message' => 'success',
            'data' => $category
        ]);
    }
}
