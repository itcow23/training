<?php

namespace app\controllers;

use app\models\forms\OrderForm;
use app\models\Order;
use app\models\search\OrderSearch;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class OrderController extends ApiController
{
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        return $searchModel->search($this->request->queryParams);
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionFilter($status)
    {
        $query = Order::find()->filterByStatus($status);

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
        $model = new Order();
        $form = new OrderForm(['scenario' => OrderForm::SCENARIO_CREATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($form->save($model)) {
                return $this->findModel($model->id);
            }
            $this->response->statusCode = 422;
            return array_merge($form->getErrors(), $model->getErrors());
        }

        throw new BadRequestHttpException('POST request required');
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $form = new OrderForm(['scenario' => OrderForm::SCENARIO_UPDATE]);

        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            if ($form->save($model)) {
                return $this->findModel($model->id);
            }
            $this->response->statusCode = 422;
            return array_merge($form->getErrors(), $model->getErrors());
        }

        throw new BadRequestHttpException('POST request required');
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $this->response->statusCode = 422;
            return $model->getErrors();
        }
        return null;
    }

    protected function findModel($id)
    {
        $model = Order::find()->andWhere(['id' => $id])->withRelations()->one();
        if ($model === null) {
            throw new NotFoundHttpException('Order not found.');
        }
        return $model;
    }
}
