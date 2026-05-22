<?php

namespace app\services;

use app\helpers\AttributeHelper;
use app\models\forms\PostCategoryForm;
use app\models\PostCategory;
use RuntimeException;
use Throwable;
use Yii;

class PostCategoryService
{
    public function create(PostCategory $model, PostCategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(PostCategory $model, PostCategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(PostCategory $model, PostCategoryForm $form)
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

    private function assignAttributes(PostCategory $model, PostCategoryForm $form): void
    {
        $pushed = $model->isNewRecord ? [] : $form->getPushedAttributes();
        AttributeHelper::map($model, $form, $pushed);

    }

    public function delete(PostCategory $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete post category.');
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
