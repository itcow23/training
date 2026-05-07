<?php

namespace app\models;

use Yii;

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
            [['title', 'content', 'published_at', 'slug', 'category_id'], 'required'],
            [['description', 'content'], 'string'],
            [['published_at', 'created_at', 'updated_at'], 'safe'],
            [['status', 'category_id'], 'integer'],
            [['title', 'thumbnail', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
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

    /**
     * Gets query for [[Ratings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatings()
    {
        return $this->hasMany(Rating::class, ['post_id' => 'id']);
    }

}
