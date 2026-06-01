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
        $model = TagForm::find()->andWhere(['id' => $id])->notDeleted()->one();
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
