<?php

namespace app\controllers;

use app\models\forms\TagForm;
use app\models\search\TagSearch;
use yii\web\NotFoundHttpException;

class TagController extends ApiController
{
    public function actionIndex()
    {
        $searchModel = new TagSearch();
        return $searchModel->search($this->request->queryParams);
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionCreate()
    {
        $model = new TagForm();

        $model->load($this->request->post(), '');

        if ($model->save()) {
            return $this->success($this->findModel($model->id), 'Tag created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->load($this->request->post(), '');

        if ($model->save()) {
            return $this->success($this->findModel($model->id), 'Tag updated successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->softDelete()) {
            $error = $model->getFirstError('is_deleted') ?: 'Không thể xóa thẻ này.';
            return $this->error($error, self::STATUS_BAD_REQUEST);
        }
        return $this->success(null, 'Deleted successfully');
    }

    protected function findModel($id)
    {
        $model = TagForm::find()->byId($id)->notDeleted()->one();
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
