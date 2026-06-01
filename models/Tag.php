<?php

namespace app\models;

use app\behaviors\SoftDeleteBehavior;
use app\models\base\BaseTag;
use app\models\query\TagQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property PostTag[] $postTags
 */
class Tag extends BaseTag
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'slug' => [
                'class' => SluggableBehavior::class,
                'ensureUnique' => true,
                'immutable' => false,
                'attribute' => 'name'
            ],
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
            ],
        ];
    }

    public function getPosts()
    {
        return $this->hasMany(Post::class, ['id' => 'post_id'])->viaTable('post_tag', ['tag_id' => 'id']);
    }

    public static function find()
    {
        return new TagQuery(get_called_class());
    }
}

