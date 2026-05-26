<?php

namespace app\controllers;

use app\models\Category;
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
        $model = new Category(['scenario' => Category::SCENARIO_CREATE]);

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Category::SCENARIO_UPDATE;

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
        $model = Category::find()->where(['id' => $id])->withRelations()->one();
        if ($model === null) {
            throw new NotFoundHttpException('Category not found.');
        }
        return $model;
    }
}
