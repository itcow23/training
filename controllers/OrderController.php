<?php

namespace app\controllers;

use app\models\forms\OrderForm;
use app\models\Order;
use app\models\response\OrderResponse;
use app\models\search\OrderSearch;
use yii\web\NotFoundHttpException;
use app\services\OrderService;


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

        return[
            'data' => $dataProvider->getModels()
        ];
    }

    /**
     * Displays a single Order model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return [
            'data' => $this->findModel($id),
        ];
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
            if ($result = $this->orderService->create($model, $form)) {
                return $this->successResponse('Create order success', ['order' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Create error');
        }
        return $this->errorResponse($form, 'Invalid request');
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
            if ($result = $this->orderService->updateStatusOrder($model, $form)) {
                return $this->successResponse('Update status success', ['order' => $result]);
            }
            return $this->errorResponse($form->hasErrors() ? $form : $model, 'Update status error');
        }

        return $this->errorResponse($form, 'Invalid request');
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
        if ($this->orderService->delete($model)) {
            return $this->successResponse('Delete success');
        }
        return $this->errorResponse($model, 'Delete failed');
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
