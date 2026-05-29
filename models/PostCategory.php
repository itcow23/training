<?php

namespace app\models;

use app\behaviors\BypassSoftDeleteBehavior;
use app\behaviors\SoftDeleteBehavior;
use app\models\query\PostCategoryQuery;
use Override;
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
class PostCategory extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_STATUS = 'update_status';
    const SCENARIO_DELETE = 'delete';

    public static $bypassDeleteFilter = false;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'status'];
        $scenarios[self::SCENARIO_UPDATE] = ['name', 'status'];
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        $scenarios[self::SCENARIO_DELETE] = ['is_deleted', 'deleted_at'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name'], 'string', 'min' => 1, 'max' => 255],
            [['name'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => [0, 1]],
            [['status'], 'default', 'value' => 1],
        ];
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

    #[Override]
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
                'value' => function (){
                     return date('Y-m-d H:i:s');
                }
            ],
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_CREATE => self::OP_INSERT,
            self::SCENARIO_UPDATE => self::OP_UPDATE,
            self::SCENARIO_UPDATE_STATUS => self::OP_UPDATE,
            self::SCENARIO_DELETE => self::OP_UPDATE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_category';
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert && isset($changedAttributes['is_deleted']) && $this->is_deleted == 1) {
            // Cascade soft delete all posts under this category!
            foreach ($this->posts as $post) {
                $post->softDelete();
            }
        }
    }

    #[Override]
    public static function find()
    {
        $query = new PostCategoryQuery(get_called_class());
        if (!self::$bypassDeleteFilter) {
            $query->andWhere(['post_category.is_deleted' => 0]);
        }
        return $query;
    }

    public static function findWithDeleted()
    {
        return new PostCategoryQuery(get_called_class());
    }
}
