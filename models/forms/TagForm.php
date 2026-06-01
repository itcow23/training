<?php

namespace app\models\forms;

use app\models\Tag;


class TagForm extends Tag
{

   public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'unique', 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
                $query->withDeleted();
            }],
        ]);
    }

}
