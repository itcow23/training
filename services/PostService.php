<?php

namespace app\services;

use app\models\Post;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class PostService
{
    public function create($model, $postData)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->load($postData, '')) {
                throw new RuntimeException('Load error');
            }
             $model->image = UploadedFile::getInstancesByName('image');
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

    public function update($model, $postData)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->load($postData, '')) {
                throw new RuntimeException('Load error');
            }
             $model->image = UploadedFile::getInstancesByName('image');
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

    public function delete($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete.');
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }
}
