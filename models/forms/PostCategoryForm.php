<?php

namespace app\models\forms;

use app\models\PostCategory;
class PostCategoryForm extends PostCategory
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['status'], 'in', 'range' => [0, 1]],
            [['name'], 'unique', 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
                $query->notDeleted();
            }],
            [['slug'], 'unique', 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
                $query->notDeleted();
            }],

        ]);
    }
}
