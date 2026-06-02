<?php

namespace app\models\forms;

use app\models\Rating;

class RatingForm extends Rating
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['post_id', 'account_id'], 'unique', 'targetAttribute' => ['post_id', 'account_id'], 'message' => 'You have already rated this post.'],
        ]);
    }
}
