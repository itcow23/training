<?php

namespace app\services;

use app\helpers\AttributeHelper;
use app\models\Category;
use app\models\forms\CategoryForm;
use RuntimeException;
use Throwable;
use Yii;

class CategoryService
{
    public function create(Category $model, CategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(Category $model, CategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(Category $model, CategoryForm $form): bool
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

    private function assignAttributes(Category $model, CategoryForm $form): void
    {

        AttributeHelper::map($model, $form, $form->getPushedAttributes());

        if ($form->image !== null) {
            $model->image = $form->image;
        }
        if ($form->removed_image !== null) {
            $model->removed_image = $form->removed_image;
        }
    }

    public function delete(Category $model): bool
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
