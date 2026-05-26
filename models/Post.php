<?php

namespace app\models;

use app\behaviors\MediaBehavior;
use app\models\query\PostQuery;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use app\models\PostTag;
use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $content
 * @property string $published_at
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

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_STATUS = 'update_status';

    public $image;
    public $removed_image;
    public $add_tag;
    public $removed_tag;

    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SluggableBehavior::class,
                'ensureUnique' => true,
                'immutable' => false,
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
                'collection' => 'image post',
                'folder' => 'post',
            ]
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->image = UploadedFile::getInstancesByName('image');
            return true;
        }
        return false;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['title', 'description', 'content', 'published_at', 'category_id', 'image', 'add_tag'];
        $scenarios[self::SCENARIO_UPDATE] = ['title', 'description', 'content', 'published_at', 'status', 'category_id', 'image', 'removed_image', 'add_tag', 'removed_tag'];
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['category_id'], 'exist', 'targetClass' => PostCategory::class, 'targetAttribute' => 'id'],
            [['title', 'content', 'category_id'], 'required'],
            [['description', 'content'], 'string'],
            [['published_at'], 'safe'],
            [['status', 'category_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_HIDDEN, self::STATUS_ARCHIVED]],
            [
                ['image'],
                'file',
                'skipOnEmpty' => true,
                'maxFiles' => 10,
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'maxSize' => 5 * 1024 * 1024,
            ],
            [['removed_image'], 'each', 'rule' => ['integer']],
            [['add_tag', 'removed_tag'], 'each', 'rule' => ['integer']],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!empty($this->removed_tag) && is_array($this->removed_tag)) {
            PostTag::deleteAll(['post_id' => $this->id, 'tag_id' => $this->removed_tag]);
        }

        if (!empty($this->add_tag) && is_array($this->add_tag)) {
            $existingTagIds = PostTag::find()
                ->select(['tag_id'])
                ->where(['post_id' => $this->id])
                ->column();

            $newTagIds = array_diff($this->add_tag, $existingTagIds);

            $rows = [];
            foreach ($newTagIds as $tagId) {
                $rows[] = [$this->id, $tagId];
            }

            if (!empty($rows)) {
                Yii::$app->db->createCommand()
                    ->batchInsert(PostTag::tableName(), ['post_id', 'tag_id'], $rows)
                    ->execute();
            }
        }
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
                return count($model->comments);
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
            },
            'tags' => function ($model) {
                return array_map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name
                    ];
                }, $model->tags);
            },
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
        return $this->hasMany(Media::class, ['file_id' => 'id'])->where(['file_type' => 'post']);
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new PostQuery(get_called_class());
    }
}
