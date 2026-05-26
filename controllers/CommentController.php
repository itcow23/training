<?php

namespace app\controllers;

use app\models\Comment;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class CommentController extends ApiController
{
    public function actionIndex()
    {
        return [
            'msg' => 'index'
        ];
    }

    public function actionCreate()
    {
        $model = new Comment(['scenario' => Comment::SCENARIO_CREATE]);

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Comment::SCENARIO_UPDATE;

        $accountId = $this->request->post('account_id');
        if ((int)$model->account_id !== (int)$accountId) {
            throw new ForbiddenHttpException('You do not have permission to edit this comment.');
        }

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $accountId = $this->request->post('account_id');
        if ((int)$model->account_id !== (int)$accountId) {
            throw new ForbiddenHttpException('You do not have permission to delete this comment.');
        }

        if (!$model->softDelete()) {
            $this->response->statusCode = 422;
            return $model->getErrors();
        }

        return null;
    }

    protected function findModel($id)
    {
        $model = Comment::find()->andWhere(['id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Comment not found.');
        }
        return $model;
    }
}
