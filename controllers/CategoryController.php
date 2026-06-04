<?php

namespace app\controllers;

use app\models\forms\CategoryForm;
use app\models\search\CategorySearch;
use Yii;
use yii\web\NotFoundHttpException;

class CategoryController extends ApiController
{
    protected const PERMISSION_VIEW = 'category.view';
    protected const PERMISSION_CREATE = 'category.create';
    protected const PERMISSION_UPDATE = 'category.update';
    protected const PERMISSION_DELETE = 'category.delete';

    protected function optionAuthActions()
    {
        return ['index', 'view'];
    }

    public function actionIndex()
    {
        if($this->request->get('show_deleted'))
        {
            $this->requirePermission(self::PERMISSION_VIEW);
        }
        $searchModel = new CategorySearch();
        return $searchModel->search($this->request->queryParams, '');
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionCreate()
    {

        $this->requirePermission(self::PERMISSION_CREATE);

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
        $this->requirePermission(self::PERMISSION_UPDATE);
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
        $this->requirePermission(self::PERMISSION_DELETE);

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
