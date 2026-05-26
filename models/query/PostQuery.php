<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Post]].
 *
 * @see \app\models\Post
 */
class PostQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Post[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Post|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function withRelations()
    {
        return $this->with([
            'comments',
            'media',
            'tags',
        ]);
    }

    public function latest()
    {
        return $this->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function keyword(?string $keyword)
    {
        return $this->andFilterWhere([
            'or',
            ['like', 'title', $keyword],
            ['like', 'content', $keyword],
            ['like', 'description', $keyword],
        ]);
    }
}

