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

        if ($model->load($this->request->post(), '') && $model->save()) {
            $category = $this->findModel($model->id);
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return [
                    'category' => $category,
                    'upload_errors' => $uploadErrors,
                ];
            }
            return $category;
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($this->request->post(), '') && $model->save()) {
            $category = $this->findModel($model->id);
            $uploadErrors = \Yii::$app->media->getErrors();
            if (!empty($uploadErrors)) {
                return [
                    'category' => $category,
                    'upload_errors' => $uploadErrors,
                ];
            }
            return $category;
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
        $model = CategoryForm::find()->notDeleted()->byId($id)->one();
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
