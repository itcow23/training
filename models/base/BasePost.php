<?php

namespace app\models\base;

use app\models\PostCategory;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $content
 * @property string|null $published_at
 * @property int $status
 * @property float|null $avg_rating
 * @property string $slug
 * @property int $category_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 */
class BasePost extends \yii\db\ActiveRecord
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
            [['description', 'published_at', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['avg_rating'], 'default', 'value' => 0.0],
            [['title', 'content', 'slug', 'category_id'], 'required'],
            [['description', 'content'], 'string'],
            [['published_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['status', 'category_id', 'is_deleted'], 'integer'],
            [['avg_rating'], 'number'],
            [['title', 'slug'], 'string', 'max' => 255],
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
            'status' => 'Status',
            'avg_rating' => 'Avg Rating',
            'slug' => 'Slug',
            'category_id' => 'Category ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'deleted_at' => 'Deleted At',
        ];
    }
}
