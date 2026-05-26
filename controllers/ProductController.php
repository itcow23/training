<?php

namespace app\controllers;

use app\models\Product;
use app\models\search\ProductSearch;
use yii\web\NotFoundHttpException;

class ProductController extends ApiController
{
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        return $searchModel->search($this->request->queryParams, '');
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionCreate()
    {
        $model = new Product(['scenario' => Product::SCENARIO_CREATE]);

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Product::SCENARIO_UPDATE;

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->delete()) {
            $this->response->statusCode = 422;
            return $model->getErrors();
        }
        return null;
    }

    protected function findModel($id)
    {
        $model = Product::find()->where(['id' => $id])->withRelations()->one();
        if ($model === null) {
            throw new NotFoundHttpException('Product not found.');
        }
        return $model;
    }
}
