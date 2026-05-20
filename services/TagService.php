<?php

namespace app\services;

use app\models\forms\TagForm;
use app\models\response\TagResponse;
use RuntimeException;
use Throwable;
use Yii;

class TagService
{
    public function create(TagResponse $model, TagForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    public function update(TagResponse $model, TagForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        return $this->save($model, $form);
    }

    private function save(TagResponse $model, TagForm $form)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->assignAttributes($model, $form);

            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return $model;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

    private function assignAttributes(TagResponse $model, TagForm $form): void
    {
        $attributes = $form->getAttributes([
            'name'
        ]);

        $model->setAttributes($attributes, false);

    }

    public function delete(TagResponse $model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete tag.');
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
