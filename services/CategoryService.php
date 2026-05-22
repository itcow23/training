<?php

namespace app\services;

use app\helpers\AttributeHelper;
use app\models\forms\CategoryForm;
use app\models\response\CategoryResponse;
use RuntimeException;
use Throwable;
use Yii;

class CategoryService
{
    public function create(CategoryResponse $model, CategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(CategoryResponse $model, CategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(CategoryResponse $model, CategoryForm $form): bool
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

    private function assignAttributes(CategoryResponse $model, CategoryForm $form): void
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

    public function delete(CategoryResponse $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Cannot delete this category.');
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
