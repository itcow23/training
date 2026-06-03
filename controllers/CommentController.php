<?php

namespace app\controllers;

use app\models\Comment;
use app\models\forms\CommentForm;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class CommentController extends ApiController
{
    protected const PERMISSION_VIEW = 'comment.view';
    protected const PERMISSION_CREATE = 'comment.create';
    protected const PERMISSION_UPDATE = 'comment.update';
    protected const PERMISSION_DELETE = 'comment.delete';
    protected const PERMISSION_UPDATE_OWN = 'comment.update_own';

    protected function optionAuthActions()
    {
        return ['index'];
    }

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

        if (!Yii::$app->user->can('comment.moderate') && (int)$model->account_id !== (int)Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to edit this comment.');
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

        if (!Yii::$app->user->can('comment.moderate') && (int)$model->account_id !== (int)Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to delete this comment.');
        }

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
