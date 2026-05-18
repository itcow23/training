<?php

namespace app\services;

use app\models\forms\PostForm;
use app\models\Post;
use app\models\response\PostResponese;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class PostService
{
    public function create(PostResponese $model, PostForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(PostResponese $model, PostForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(PostResponese $model, PostForm $form)
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
            return PostResponese::findOne($model->id);
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    private function assignAttributes(PostResponese $model, PostForm $form): void
    {
        $attributes = $form->getAttributes([
            'category_id', 'title', 'content', 'status', 'description',
        ]);

        $model->setAttributes($attributes, false);

        if ($form->removed_image !== null && is_array($form->removed_image)) {
            $model->removed_image = $form->removed_image;
        }
    }

    public function delete(PostResponese $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete product.');
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    public function updateStatus($model, $postData)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->load($postData, '')) {
                throw new RuntimeException('Load error');
            }

            if ($model->status == Post::STATUS_PUBLISHED && empty($model->published_at)) {
                $model->published_at = date('Y-m-d H:i:s');
            }


            if (!$model->save()) {
                throw new RuntimeException('Save error');
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

}
