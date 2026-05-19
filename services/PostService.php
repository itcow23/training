<?php

namespace app\services;

use app\models\forms\PostForm;
use app\models\Post;
use app\models\response\PostResponse;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class PostService
{
    public function create(PostResponse $model, PostForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(PostResponse $model, PostForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(PostResponse $model, PostForm $form)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->assignAttributes($model, $form);
            $model->image = UploadedFile::getInstancesByName('image');

            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return PostResponse::find()
                ->where(['id' => $model->id])
                ->with(['comments', 'ratings', 'media'])
                ->one();
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    private function assignAttributes(PostResponse $model, PostForm $form): void
    {
        $attributes = $form->getAttributes([
            'category_id', 'title', 'content', 'status', 'description',
        ]);

        $model->setAttributes($attributes, false);

        if ($form->removed_image !== null && is_array($form->removed_image)) {
            $model->removed_image = $form->removed_image;
        }
    }

    public function delete(PostResponse $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete post.');
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    public function updateStatus($model, $form)
    {
       if (!$form->validate()) {
            return false;
        }
       $transaction = Yii::$app->db->beginTransaction();
        try {
           $model->setAttributes($form->getAttributes(['status']), false);

            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return PostResponse::find()
                ->where(['id' => $model->id])
                ->with(['comments', 'ratings', 'media'])
                ->one();
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

}
