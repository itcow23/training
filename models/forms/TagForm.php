<?php

namespace app\models\forms;

use app\models\Tag;


class TagForm extends Tag
{

   public function rules()
    {
        $model = $this;
        return array_merge(parent::rules(), [
            [['name'], 'unique', 'filter' => function ($query) use ($model) {
                if (!$model->isNewRecord) {
                    $query->andWhere(['not', ['id' => $model->id]]);
                }
                $query->withDeleted();
            }],
        ]);
    }

}
