<?php

namespace app\controllers;

use app\models\Rating;
use app\models\forms\RatingForm;
use yii\web\NotFoundHttpException;

class RatingController extends ApiController
{
    public function actionIndex()
    {
        return ['message' => 'Index rating'];
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function actionCreate()
    {
        $model = new RatingForm();

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    protected function findModel($id)
    {
        $model = RatingForm::find()->andWhere(['id' => $id])->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Rating not found.');
    }
}  
