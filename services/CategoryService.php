<?php

namespace app\services;

use app\models\forms\CategoryForm;
use app\models\response\CategoryResponse;
use RuntimeException;
use Throwable;
use Yii;
use yii\web\UploadedFile;

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

    private function save(CategoryResponse $model, CategoryForm $form)
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
            return CategoryResponse::find()
                ->where(['id' => $model->id])
                ->with(['products', 'media'])
                ->one();
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    private function assignAttributes(CategoryResponse $model, CategoryForm $form): void
    {
        $model->setAttributes($form->getAttributes(['name']), false);

        if ($form->removed_image !== null && is_array($form->removed_image)) {
            $model->removed_image = $form->removed_image;
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
