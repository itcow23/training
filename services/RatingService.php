<?php

namespace app\services;

use app\helpers\AttributeHelper;
use app\models\forms\RatingForm;
use app\models\Rating;
use RuntimeException;
use Throwable;
use Yii;

class RatingService
{
    public function create(Rating $model, RatingForm $form)
    {
        if (!$form->validate()) {
            return false;
        }

        $exists = Rating::find()
            ->where(['post_id' => $form->post_id, 'account_id' => $form->account_id])
            ->exists();

        if ($exists) {
            $form->addError('account_id', 'You have already rated this post.');
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $pushed = $model->isNewRecord ? [] : $form->getPushedAttributes();
            AttributeHelper::map($model, $form, $pushed);

            if (!$model->save()) {
                throw new RuntimeException('Save error');
            }

            $transaction->commit();
            return $model;
        } catch (Throwable $e) {
            $transaction->rollBack();
            $model->addError('error', $e->getMessage());
            return false;
        }
    }

}
