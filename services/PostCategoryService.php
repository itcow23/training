<?php

namespace app\services;

use app\models\forms\PostCategoryForm;
use app\models\response\PostCategoryResponse;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class PostCategoryService
{
    public function create(PostCategoryResponse $model, PostCategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(PostCategoryResponse $model, PostCategoryForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(PostCategoryResponse $model, PostCategoryForm $form)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->assignAttributes($model, $form);

            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return PostCategoryResponse::findOne($model->id);
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    private function assignAttributes(PostCategoryResponse $model, PostCategoryForm $form): void
    {
        $attributes = $form->getAttributes([
            'name'
        ]);

        $model->setAttributes($attributes, false);

    }

    public function delete(PostCategoryResponse $model): bool
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
