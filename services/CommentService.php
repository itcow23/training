<?php

namespace app\services;

use app\models\Comment;
use app\models\forms\CommentForm;
use RuntimeException;
use Throwable;
use Yii;

class CommentService
{
    public function create(Comment $model, CommentForm $form)
    {
        if (!$form->validate()) {
            return false;
        }


        $transaction = Yii::$app->db->beginTransaction();
        try {

            $model->setAttributes($form->getAttributes(), false);

            if (!$model->save()) {
                throw new RuntimeException('Save error');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    public function update(Comment $model, CommentForm $form)
    {
        if (!$form->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            if ($model->account_id != $form->account_id) {
                throw new RuntimeException('You do not have permission to edit this comment.');
            }

            $model->setAttributes($form->getAttributes(['content']), false);

            if (!$model->save()) {
                throw new RuntimeException('update error');
            }

            $transaction->commit();
            return $model;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $form->addError('error', $e->getMessage());
            return false;
        }
    }

    public function delete(Comment $model, CommentForm $form)
    {
        if (!$form->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ((int)$model->account_id !== (int)$form->account_id) {
                throw new RuntimeException('You do not have permission to delete this comment.');
            }

            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete.');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $form->addError('error', $e->getMessage());
            return false;
        }
    }
}
