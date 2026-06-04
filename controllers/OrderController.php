<?php

namespace app\controllers;

use app\models\forms\OrderForm;
use app\models\Order;
use app\models\search\OrderSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class OrderController extends ApiController
{
    protected const PERMISSION_VIEW = 'order.view';
    protected const PERMISSION_CREATE = 'order.create';
    protected const PERMISSION_UPDATE = 'order.update';
    protected const PERMISSION_DELETE = 'order.delete';
    protected const PERMISSION_UPDATE_STATUS = 'order.update_status';

    public function actionIndex()
    {
        $this->requirePermission(self::PERMISSION_VIEW);

        $searchModel = new OrderSearch();
        return $searchModel->search($this->request->queryParams, '');
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('order.view') && (int)Yii::$app->user->id !== (int)$model->account_id) {
            throw new \yii\web\ForbiddenHttpException('You do not have permission to view this order.');
        }

        return $model;
    }

    public function actionFilter($status)
    {
        $this->requirePermission(self::PERMISSION_VIEW);

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

        $form->load($this->request->post(), '');

        if ($form->save()) {
            return $this->success($this->findModel($form->id), 'Order created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $form);
    }

    public function actionUpdateStatus($id)
    {
        $this->requirePermission(self::PERMISSION_UPDATE_STATUS);

        $model = $this->findModel($id);
        $newStatus = (int)$this->request->post('status');

        if (!$model->validateStatusTransition($newStatus)) {
            return $this->error('Transition not allowed.', self::STATUS_UNPROCESSABLE_ENTITY);
        }

        $model->status = $newStatus;
        if (!$model->save()) {
            return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
        }

        return $this->success(null, 'Status updated successfully.');
    }

    public function actionDelete($id)
    {
        $this->requirePermission(self::PERMISSION_DELETE);

        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $error = $model->getFirstError('is_deleted') ?: 'Không thể xóa đơn hàng này.';
            return $this->error($error, self::STATUS_BAD_REQUEST);
        }
        return $this->success(null, 'Order deleted successfully.');
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
