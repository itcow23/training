<?php

namespace app\controllers;

use app\models\forms\PostForm;
use app\models\search\PostSearch;
use yii\web\NotFoundHttpException;

class PostController extends ApiController
{
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        return $searchModel->search($this->request->queryParams);
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionCreate()
    {
        $model = new PostForm();

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
        $model = PostForm::find()->byId($id)->notDeleted()->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Post not found.');
    }
}
