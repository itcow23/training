<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Product]].
 *
 * @see \app\models\Product
 */
class ProductQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Product[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Product|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->joinWith('category', false)
            ->andWhere(['product.status' => 1])
            ->andWhere(['category.status' => 1])
            ->andWhere(['category.is_deleted' => 0]);
    }

    public function inactive()
    {
        return $this->andWhere(['product.status' => 0]);
    }

     public function byId($id)
    {
        return $this->andWhere([
            'product.id' => $id,
        ]);
    }

    public function withActiveCategory()
    {
        return $this->joinWith('category', false)
            ->andWhere(['category.status' => 1])
            ->andWhere(['category.is_deleted' => 0]);
    }

    public function byCategory($category)
    {
        return $this->andWhere(['product.category_id' => $category]);
    }

    public function keyword(?string $keyword)
    {
        return $this->andFilterWhere([
            'like',
            'product.name',
            $keyword,
        ]);
    }

    public function notDeleted()
    {
        return $this->andWhere(['product.is_deleted' => 0]);
    }

    public function deleted()
    {
        return $this->andWhere(['product.is_deleted' => 1]);
    }

    public function withDeleted()
    {
        return $this->andWhere([]);
    }


}
