<?php

namespace app\models\query;

use yii\db\ActiveQuery;

class CategoryQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['status' => 1]);
    }

    public function latest()
    {
        return $this->orderBy(['id' => SORT_DESC]);
    }

    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }

    public function keyword(?string $keyword)
    {
        return $this->andFilterWhere(['like', 'name', $keyword]);
    }

     public function notDeleted()
    {
        return $this->andWhere(['is_deleted' => 0]);
    }

    public function deleted()
    {
        return $this->andWhere(['is_deleted' => 1]);
    }

    public function withDeleted()
    {
        return $this;
    }
}
