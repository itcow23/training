<?php

namespace app\services;

use app\models\Product;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class ProductService
{
    public function create(Product $model, array $postData)
    {

        $transaction = Yii::$app->db->beginTransaction();
        try {

            if (!$model->load($postData,'')) {
               throw new RuntimeException('Load fail');
            }

            $model->image = UploadedFile::getInstancesByName('image');

            if (!$model->save()) {
                throw new RuntimeException('Failed to save Product.');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('model', $e->getMessage());
            return false;
        }
    }

    public function update(Product $model, array $postData): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            if (!$model->load($postData,'')) {
               throw new RuntimeException('Load fail');
            }

            $model->image = UploadedFile::getInstancesByName('image');

            if (!$model->save()) {
                throw new RuntimeException('Failed to save Product.');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('image', $e->getMessage());
            return false;
        }
    }

    public function delete(Product $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete category.');
            }

            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            return false;
        }
    }
}
