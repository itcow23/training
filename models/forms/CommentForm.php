<?php

namespace app\models\forms;

use app\models\Comment;

class CommentForm extends Comment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return parent::rules();
    }
}
