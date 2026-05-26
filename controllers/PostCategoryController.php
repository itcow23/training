<?php

namespace app\controllers;

use app\models\PostCategory;
use app\models\search\PostCategorySearch;
use yii\web\NotFoundHttpException;

class PostCategoryController extends ApiController
{
    public function actionIndex()
    {
        $searchModel = new PostCategorySearch();
        return $searchModel->search($this->request->queryParams, '');
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionCreate()
    {
        $model = new PostCategory(['scenario' => PostCategory::SCENARIO_CREATE]);

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = PostCategory::SCENARIO_UPDATE;

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
        $model = PostCategory::find()->andWhere(['id' => $id])->withRelations()->one();
        if ($model === null) {
            throw new NotFoundHttpException('Post category not found.');
        }
        return $model;
    }
}
