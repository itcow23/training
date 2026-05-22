<?php

namespace app\services;

use app\helpers\AttributeHelper;
use app\models\forms\ProductForm;
use app\models\response\ProductResponse;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class ProductService
{
    public function create(ProductResponse $model, ProductForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(ProductResponse $model, ProductForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(ProductResponse $model, ProductForm $form)
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
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    private function assignAttributes(ProductResponse $model, ProductForm $form): void
    {
        $attributes = AttributeHelper::filter($form->getAttributes());

        $model->setAttributes($attributes, false);

        if (isset($attributes['removed_image'])) {
            $model->removed_image = $attributes['removed_image'];
        }

        if (isset($attributes['image'])) {
            $model->image = $attributes['image'];
        }
    }

    public function delete(ProductResponse $model): bool
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
}
