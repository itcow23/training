<?php

namespace app\models;

use app\behaviors\SoftDeleteBehavior;
use app\models\base\BasePostCategory;
use app\models\query\PostCategoryQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "post_category".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Post[] $posts
 */
class PostCategory extends BasePostCategory
{
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SluggableBehavior::class,
                'ensureUnique' => true,
                'immutable' => false,
                'attribute' => 'name'
            ],
            'timestamps' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert && isset($changedAttributes['is_deleted']) && $this->is_deleted == 1) {
            foreach ($this->posts as $post) {
                $post->softDelete();
            }
        }
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'status',
            'posts' => function ($model) {
                return array_map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'description' => $post->description,
                        'content' => $post->content,
                        'status' => $post->status,
                        'published_at' => $post->published_at
                    ];
                }, $model->posts);
            },
        ];
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['category_id' => 'id']);
    }

    public static function find()
    {
        $query = new PostCategoryQuery(get_called_class());
        return $query->notDeleted();
    }

    public static function findWithDeleted()
    {
        return new PostCategoryQuery(get_called_class());
    }
}
