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
        $model = new ProductResponse();

        return [
            'items' => $dataProvider->getModels(),
            'product active' => $model->find()
                                ->with(['category','media'])
                                ->active()
                                ->all(),
            'pagination' => [
                'total' => $dataProvider->getTotalCount(),
                'page' => $dataProvider->pagination->page + 1,
                'pageSize' => $dataProvider->pagination->pageSize,
                'pageCount' => $dataProvider->pagination->getPageCount(),
            ]
        ];
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        return [
            'model' => $this->findModel($id),
        ];
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
                return $this->successResponse('Created successfully', ['model' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Failed to create');
        }

        return $this->errorResponse($form, 'Invalid request');
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
                return $this->successResponse('Updated successfully', ['model' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Failed to update');
        }

        return $this->errorResponse($form, 'Invalid request');
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
            return $this->errorResponse($model, 'Delete error');
        }

        return $this->successResponse('Delete success');
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
