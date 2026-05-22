<?php

namespace app\controllers;

use app\models\forms\OrderForm;
use app\models\Order;
use app\models\response\OrderResponse;
use app\models\search\OrderSearch;
use yii\web\NotFoundHttpException;
use app\services\OrderService;
use yii\data\ActiveDataProvider;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends BaseController
{
    public OrderService $orderService;

    public function init()
    {
        $this->orderService = new OrderService();
        return parent::init();
    }

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->dataProviderResponse($dataProvider, 'Orders retrieved successfully');
    }

    /**
     * Displays a single Order model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->successResponse(
            ['order' => $this->findModel($id)],
            'Order retrieved successfully'
        );
    }

    public function actionFilter($status)
    {
        $query = OrderResponse::find()->filterByStatus($status);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        return $this->dataProviderResponse($dataProvider, 'Orders filtered by status retrieved successfully');
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new OrderResponse();
        $form = new OrderForm(['scenario' => OrderForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($this->orderService->create($model, $form)) {
                return $this->successResponse(
                    ['order' => $this->findModel($model->id)],
                    'Order created successfully',
                    201
                );
            }
            return $this->modelErrorResponse([$form, $model], 'Failed to create order');
        }

        return $this->errorResponse('POST request required', 'Invalid request', 400);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $form = new OrderForm(['scenario' => OrderForm::SCENARIO_UPDATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($this->orderService->updateStatusOrder($model, $form)) {
                return $this->successResponse(
                    ['order' => $this->findModel($model->id)],
                    'Order status updated successfully'
                );
            }
            return $this->modelErrorResponse([$form, $model], 'Failed to update order status');
        }

        return $this->errorResponse('POST request required', 'Invalid request', 400);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
       if(!$this->orderService->delete($model)){
           return $this->modelErrorResponse([$model], 'Failed to delete order');
       }
        return $this->successResponse([], 'Order deleted successfully'
        );
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        return parent::findModelByClass(OrderResponse::class, $id);
    }
}
