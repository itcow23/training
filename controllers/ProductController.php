<?php

namespace app\controllers;

use app\models\forms\ProductForm;
use app\models\Product;
use app\models\response\ProductResponse;
use app\models\search\ProductSearch;
use yii\web\NotFoundHttpException;
use app\services\ProductService;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends BaseController
{
    private ProductService $productService;

    public function init()
    {
        $this->productService = new ProductService();
        return parent::init();
    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->listResponse(
            $dataProvider->getModels(),
            $dataProvider->getTotalCount(),
            $dataProvider->pagination->getPage() + 1,
            $dataProvider->pagination->getPageSize(),
            $dataProvider->pagination->getPageCount(),
            'Products retrieved successfully'
        );
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->successResponse(
            ['model' => $this->findModel($id)],
            'Product retrieved successfully'
        );
    }

    public function actionListInactive()
    {
        $models = ProductResponse::find()->inactive()->all();
        return $this->successResponse(
            ['items' => $models],
            'Inactive products retrieved successfully'
        );
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ProductResponse();
        $form = new ProductForm(['scenario' => ProductForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->productService->create($model, $form)) {
                return $this->successResponse(
                    ['model' => $result],
                    'Product created successfully',
                    201
                );
            }
            return $this->errorResponse(
                $form->hasErrors() ? $form : $model,
                'Failed to create product',
                422
            );
        }

        return $this->errorResponse(
            ['message' => 'POST request required'],
            'Invalid request',
            400
        );
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $form = new ProductForm(['scenario' => ProductForm::SCENARIO_UPDATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($result = $this->productService->update($model, $form)) {
                return $this->successResponse(
                    ['model' => $result],
                    'Product updated successfully'
                );
            }
            return $this->errorResponse(
                $form->hasErrors() ? $form : $model,
                'Failed to update product',
                422
            );
        }

        return $this->errorResponse(
            ['message' => 'POST request required'],
            'Invalid request',
            400
        );
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$this->productService->delete($model)) {
            return $this->errorResponse(
                $model->hasErrors() ? $model->errors : ['message' => 'Failed to delete'],
                'Delete failed',
                400
            );
        }

        return $this->successResponse(
            [],
            'Product deleted successfully',
            204
        );
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        return parent::findModelByClass(ProductResponse::class, $id);
    }
}
