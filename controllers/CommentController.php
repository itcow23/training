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

        $model->load($this->request->post(), '');

        if ($model->save()) {
            return $this->success($this->findModel($model->id), 'Comment created successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
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

        $model->load($this->request->post(), '');

        if ($model->save()) {
            return $this->success($this->findModel($model->id), 'Comment updated successfully');
        }

        return $this->error('Validation failed', self::STATUS_UNPROCESSABLE_ENTITY, $model);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $accountId = $this->request->post('account_id');
        if ((int)$model->account_id !== (int)$accountId) {
            throw new ForbiddenHttpException('You do not have permission to delete this comment.');
        }

        if (!$model->softDelete()) {
            $error = $model->getFirstError('is_deleted') ?: 'Không thể xóa bình luận này.';
            return $this->error($error, self::STATUS_BAD_REQUEST);
        }

        return $this->success(null, 'Deleted successfully');
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
