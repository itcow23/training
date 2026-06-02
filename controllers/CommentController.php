<?php

namespace app\controllers;

use app\models\Comment;
use app\models\forms\CommentForm;
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
        $model = new CommentForm();

        if ($model->load($this->request->post(), '') && $model->save()) {
            return $this->findModel($model->id);
        }

        $this->response->statusCode = 422;
        return $model->getErrors();
    }

    public function actionUpdate($id)
    {
        $model = CommentForm::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Comment not found.');
        }

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
        $model = CommentForm::find()->andWhere(['id' => $id])->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Comment not found.');
    }
}
