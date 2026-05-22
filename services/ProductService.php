<?php

namespace app\services;

use app\helpers\AttributeHelper;
use app\models\forms\ProductForm;
use app\models\Product;
use RuntimeException;
use Throwable;
use Yii;

class ProductService
{
    public function create(Product $model, ProductForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(Product $model, ProductForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(Product $model, ProductForm $form)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->assignAttributes($model, $form);

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

    private function assignAttributes(Product $model, ProductForm $form): void
    {
        $pushed = $model->isNewRecord ? [] : $form->getPushedAttributes();
        AttributeHelper::map($model, $form, $pushed);

        if ($form->image !== null) {
            $model->image = $form->image;
        }
        if ($form->removed_image !== null) {
            $model->removed_image = $form->removed_image;
        }
    }

    public function delete(Product $model): bool
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
