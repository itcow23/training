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

        $model->load($this->request->post(), '');

        if ($model->save()) {
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return $this->error('Post created but image upload failed.', self::STATUS_BAD_REQUEST, $uploadErrors);
            }
            return $this->success($this->findModel($model->id), 'Post created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->load($this->request->post(), '');

        if ($model->save()) {
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return $this->error('Post updated but image upload failed.', self::STATUS_BAD_REQUEST, $uploadErrors);
            }
            return $this->success($this->findModel($model->id), 'Post updated successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $error = $model->getFirstError('is_deleted') ?: 'Không thể xóa bài viết này.';
            return $this->error($error, self::STATUS_BAD_REQUEST);
        }
        return $this->success(null, 'Deleted successfully');
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
