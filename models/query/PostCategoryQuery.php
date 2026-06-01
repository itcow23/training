<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\PostCategory]].
 *
 * @see \app\models\PostCategory
 */
class PostCategoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\PostCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\PostCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function withRelations()
    {
        return $this->with([
            'posts',
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

    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
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


