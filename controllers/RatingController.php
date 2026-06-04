<?php

namespace app\controllers;

use app\models\Rating;
use app\models\forms\RatingForm;
use yii\web\NotFoundHttpException;

class RatingController extends ApiController
{
    protected function optionAuthActions()
    {
        return ['index', 'view'];
    }

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

        $model->load($this->request->post(), '');

        if ($model->save()) {
            return $this->success($this->findModel($model->id), 'Rating created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
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
