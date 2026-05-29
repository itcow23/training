<?php

namespace app\controllers;

use app\models\forms\CategoryForm;
use app\models\search\CategorySearch;
use yii\web\NotFoundHttpException;

class CategoryController extends ApiController
{
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        return $searchModel->search($this->request->queryParams, '');
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionCreate()
    {
        $model = new CategoryForm();

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
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
        if (($model = CategoryForm::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
