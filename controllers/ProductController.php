<?php

namespace app\controllers;

use app\models\forms\ProductForm;
use app\models\search\ProductSearch;
use yii\web\NotFoundHttpException;

class ProductController extends ApiController
{
    protected const PERMISSION_VIEW = 'product.view';
    protected const PERMISSION_CREATE = 'product.create';
    protected const PERMISSION_UPDATE = 'product.update';
    protected const PERMISSION_DELETE = 'product.delete';

    protected function optionAuthActions()
    {
        return ['index', 'view'];
    }

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
        $this->requirePermission(self::PERMISSION_CREATE);

        $model = new ProductForm();

        $model->load($this->request->post(), '');

        if ($model->save()) {
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return $this->error('Product created but image upload failed.', self::STATUS_BAD_REQUEST, $uploadErrors);
            }
            return $this->success($this->findModel($model->id), 'Product created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionUpdate($id)
    {
        $this->requirePermission(self::PERMISSION_UPDATE);

        $model = $this->findModel($id);

        $model->load($this->request->post(), '');

        if ($model->save()) {
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return $this->error('Product updated but image upload failed.', self::STATUS_BAD_REQUEST, $uploadErrors);
            }
            return $this->success($this->findModel($model->id), 'Product updated successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionDelete($id)
    {
        $this->requirePermission(self::PERMISSION_DELETE);
        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $error = $model->getFirstError('is_deleted') ?: 'Không thể xóa sản phẩm này.';
            return $this->error($error, self::STATUS_BAD_REQUEST);
        }
        return $this->success(null, 'Deleted successfully');
    }

    protected function findModel($id)
    {
        $model = ProductForm::find()->byId($id)->notDeleted()->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Product not found.');
    }
}
