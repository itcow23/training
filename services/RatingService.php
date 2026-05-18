<?php

namespace app\services;

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
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $postData = $form->getAttributes(['post_id', 'account_id', 'score']);
            $model->setAttributes($postData, false);

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
