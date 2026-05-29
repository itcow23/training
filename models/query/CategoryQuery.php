<?php

namespace app\models\query;

use yii\db\ActiveQuery;

class CategoryQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'status' => 1,
        ]);
    }

    public function latest()
    {
        return $this->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function withRelations()
    {
        return $this->with([
            'products.category',
            'products.media',
            'products.posts',
            'media',
        ]);
    }

    public function byId($id)
    {
        return $this->andWhere([
            'id' => $id,
        ]);
    }

    public function keyword(?string $keyword)
    {
        return $this->andFilterWhere([
            'like',
            'name',
            $keyword,
        ]);
    }

    public function notDeleted()
    {
        return $this->andWhere([
            'is_deleted' => 0,
        ]);
    }
}
