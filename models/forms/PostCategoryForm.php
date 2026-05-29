<?php

namespace app\models\forms;

use app\models\base\BasePostCategory;
use app\models\PostCategory;


class PostCategoryForm extends PostCategory
{
    public function rules()
    {
        $model = $this;
        return array_merge(parent::rules(), [
            [['status'], 'in', 'range' => [0, 1]],
            [['name'], 'unique', 'targetClass' => BasePostCategory::class, 'filter' => function ($query) use ($model) {
                if (!$model->isNewRecord) {
                    $query->andWhere(['not', ['id' => $model->id]]);
                }
            }],
            [['slug'], 'unique', 'targetClass' => BasePostCategory::class, 'filter' => function ($query) use ($model) {
                if (!$model->isNewRecord) {
                    $query->andWhere(['not', ['id' => $model->id]]);
                }
            }],

        ]);
    }
}
