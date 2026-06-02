<?php

namespace app\controllers;

use app\models\forms\OrderForm;
use app\models\Order;
use app\models\search\OrderSearch;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class OrderController extends ApiController
{
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        return $searchModel->search($this->request->queryParams, '');
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionFilter($status)
    {
        $query = Order::find()->filterByStatus((int) $status)->with('membershipLevel')->notDeleted();

        return new ActiveDataProvider([
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
    }

    public function actionCreate()
    {
        $form = new OrderForm();

        if ($this->request->isPost && $form->load($this->request->post(), '') && $form->save()) {
            return $this->findModel($form->id);
        }

        $this->response->statusCode = 422;
        return $form->getErrors();
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $newStatus = (int)$this->request->post('status');

        if (!$model->validateStatusTransition($newStatus)) {
            $this->response->statusCode = 422;
            return [
                'success' => false,
                'message' => 'Transition not allowed.',
            ];
        }

        $model->status = $newStatus;
        if (!$model->save()) {
            $this->response->statusCode = 422;
            return $model->getErrors();
        }

        return [
            'success' => true,
            'message' => 'Status updated successfully.',
        ];
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $this->response->statusCode = 422;
            return $model->getErrors();
        }
        return [
            'success' => true,
            'message' => 'Order deleted successfully.',
        ];
    }

    protected function findModel($id)
    {
        $model = Order::find()->andWhere(['id' => $id])->with('membershipLevel')->notDeleted()->one();
        if ($model === null) {
            throw new NotFoundHttpException('Order not found.');
        }
        return $model;
    }
}
