<?php

namespace app\controllers;

use app\models\forms\PostCategoryForm;
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
        $model = new PostCategoryForm();

        $model->load($this->request->post(), '');

        if ($model->save()) {
            return $this->success($this->findModel($model->id), 'Post category created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->load($this->request->post(), '');

        if ($model->save()) {
            return $this->success($this->findModel($model->id), 'Post category updated successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $error = $model->getFirstError('is_deleted') ?: 'Không thể xóa danh mục bài viết này.';
            return $this->error($error, self::STATUS_BAD_REQUEST);
        }
        return $this->success(null, 'Deleted successfully');
    }

    protected function findModel($id)
    {
        $model = PostCategoryForm::find()->notDeleted()->byId($id)->one();
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
