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

        $model->load($this->request->post(), '');

        if ($model->save()) {
            $category = $this->findModel($model->id);
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return $this->error('Category created but image upload failed.', self::STATUS_BAD_REQUEST, $uploadErrors);
            }
            return $this->success($category, 'Category created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->load($this->request->post(), '');

        if ($model->save()) {
            $category = $this->findModel($model->id);
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return $this->error('Category updated but image upload failed.', self::STATUS_BAD_REQUEST, $uploadErrors);
            }
            return $this->success($category, 'Category updated successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $error = $model->getFirstError('is_deleted') ?: 'Không thể xóa danh mục này.';
            return $this->error($error, self::STATUS_BAD_REQUEST);
        }
        return $this->success(null, 'Deleted successfully');
    }

   protected function findModel($id)
    {
        $model = CategoryForm::find()->notDeleted()->byId($id)->one();
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
