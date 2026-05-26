<?php

namespace app\controllers;

use app\models\Post;
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
        $model = new Post(['scenario' => Post::SCENARIO_CREATE]);

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Post::SCENARIO_UPDATE;

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Post::SCENARIO_UPDATE_STATUS;

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
        $model = Post::find()->where(['id' => $id])->withRelations()->one();
        if ($model === null) {
            throw new NotFoundHttpException('Post not found.');
        }
        return $model;
    }
}
