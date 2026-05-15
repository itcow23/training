<?php

namespace app\models;

use app\behaviors\SlugBehavior;
use app\models\query\PostCategoryQuery;
use Override;
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

    #[Override]
    public function behaviors()
    {
        return [
            SlugBehavior::class,
            'timestamps' => [
                'class' => TimestampBehavior::class,
                'value' => function (){
                     return date('Y-m-d H:i:s');
                }
            ]
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['name', 'slug'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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

    #[Override]
    public static function find()
    {
        return new PostCategoryQuery(get_called_class());
    }

}
