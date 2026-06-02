<?php

namespace app\models;

use app\behaviors\DateTimeBehavior;
use app\behaviors\MediaBehavior;
use app\behaviors\SlugBehavior;
use app\behaviors\SoftDeleteBehavior;
use app\models\base\BasePost;
use app\models\query\PostQuery;

class Post extends BasePost
{
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SlugBehavior::class,
                'attribute' => 'title',
            ],
            'timestamp' => [
                'class' => DateTimeBehavior::class,
            ],
            'media' => [
                'class' => MediaBehavior::class,
                'collection' => 'image post',
                'folder' => 'post',
            ],
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
            ],
        ];
    }


    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'description',
            'content',
            'status',
            'published_at',
            'comment' => function ($model) {
                if ($model->isRelationPopulated('comments')) {
                    return count($model->comments);
                }
                return (int)$model->getComments()->count();
            },
            'rating' => function ($model) {
                return $model->avg_rating;
            },
            'media' => function ($model) {
                return array_map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_id' => $media->file_id,
                        'file_type' => $media->file_type,
                        'path' => $media->filepath
                    ];
                }, $model->media);
            }
        ];
    }

    public function extraFields()
    {
        return [
            'tags',
            'products',
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * Gets query for [[Accounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['id' => 'account_id'])->viaTable('rating', ['post_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(PostCategory::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['post_id' => 'id']);
    }

    /**
     * Gets query for [[PostProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostProducts()
    {
        return $this->hasMany(PostProduct::class, ['post_id' => 'id']);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])->viaTable('post_product', ['post_id' => 'id']);
    }

    /**
     * Gets query for [[PostTags]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable('post_tag', ['post_id' => 'id']);
    }

    /**
     * Gets query for [[Ratings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatings()
    {
        return $this->hasMany(Rating::class, ['post_id' => 'id']);
    }

      public function getMedia()
    {
        return $this->hasMany(Media::class, ['file_id' => 'id'])->andWhere(['file_type' => 'post']);
    }

    public static function find()
    {
        return new PostQuery(get_called_class());
    }
}
