<?php

namespace app\models;

use app\behaviors\MediaBehavior;
use app\behaviors\SlugBehavior;
use app\models\query\PostQuery;
use Override;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $content
 * @property string $published_at
 * @property string|null $thumbnail
 * @property int $status
 * @property string $slug
 * @property int $category_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account[] $accounts
 * @property PostCategory $category
 * @property Comment[] $comments
 * @property PostProduct[] $postProducts
 * @property PostTag[] $postTags
 * @property Rating[] $ratings
 */
class Post extends \yii\db\ActiveRecord
{


    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_HIDDEN = 2;
    const STATUS_ARCHIVED = 3;

    public $image;
    public $removed_image;
    #[Override]
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SlugBehavior::class,
                'attribute' => 'title'
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'media' => [
                'class' => MediaBehavior::class,
                'collection' => 'image post'
            ]
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'thumbnail', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 0],
            [['title', 'content', 'slug', 'category_id'], 'required'],
            [['description', 'content'], 'string'],
            [['published_at', 'created_at', 'updated_at','removed_image'], 'safe'],
            [['status', 'category_id'], 'integer'],
            [['title', 'thumbnail', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['image'], 'file', 'maxFiles' => 10, 'skipOnEmpty' => true],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PostCategory::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'published_at' => 'Published At',
            'thumbnail' => 'Thumbnail',
            'status' => 'Status',
            'slug' => 'Slug',
            'category_id' => 'Category ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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

    /**
     * Gets query for [[PostTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostTags()
    {
        return $this->hasMany(PostTag::class, ['post_id' => 'id']);
    }

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
        return $this->hasMany(Media::class, ['file_id' => 'id'])->where(['file_type' => 'post']);
    }

    #[Override]
    public static function find()
    {
        return new PostQuery(get_called_class());
    }
}
