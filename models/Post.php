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
    public $add_product;
    public $removed_product;
    public static $bypassDeleteFilter = false;

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
            ],
            'softDelete' => [
                'class' => \app\behaviors\SoftDeleteBehavior::class,
            ],
            'bypassSoftDelete' => [
                'class' => \app\behaviors\BypassSoftDeleteBehavior::class,
            ],
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
        $scenarios[self::SCENARIO_CREATE] = ['title', 'description', 'content', 'published_at', 'category_id', 'image', 'add_tag', 'add_product'];
        $scenarios[self::SCENARIO_UPDATE] = ['title', 'description', 'content', 'published_at', 'status', 'category_id', 'image', 'removed_image', 'add_tag', 'removed_tag', 'add_product', 'removed_product'];
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
            [['add_tag', 'removed_tag', 'add_product', 'removed_product'], 'each', 'rule' => ['integer']],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->syncRelation(PostTag::class, 'tag_id', $this->add_tag, $this->removed_tag);

        $this->syncRelation(PostProduct::class, 'product_id', $this->add_product, $this->removed_product);
    }

    protected function syncRelation(string $modelClass, string $fkTargetColumn, ?array $addIds, ?array $removedIds)
    {
        if (!empty($removedIds) && is_array($removedIds)) {
            $modelClass::deleteAll(['post_id' => $this->id, $fkTargetColumn => $removedIds]);
        }

        if (!empty($addIds) && is_array($addIds)) {
            $existingIds = $modelClass::find()
                ->select([$fkTargetColumn])
                ->where(['post_id' => $this->id])
                ->column();

            $newIds = array_diff($addIds, $existingIds);

            $rows = [];
            $tableName = $modelClass::tableName();
            $hasCreatedAt = $modelClass::getTableSchema()->getColumn('created_at') !== null;

            foreach ($newIds as $targetId) {
                if ($hasCreatedAt) {
                    $rows[] = [$this->id, $targetId, date('Y-m-d H:i:s')];
                } else {
                    $rows[] = [$this->id, $targetId];
                }
            }

            if (!empty($rows)) {
                $columns = $hasCreatedAt ? ['post_id', $fkTargetColumn, 'created_at'] : ['post_id', $fkTargetColumn];
                Yii::$app->db->createCommand()
                    ->batchInsert($tableName, $columns, $rows)
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
            },
            'tags' => function ($model) {
                return array_map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name
                    ];
                }, $model->tags);
            },
            'products' => function ($model) {
                return array_map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => (float)$product->price,
                        'discount' => (int)$product->discount,
                        'slug' => $product->slug,
                    ];
                }, $model->products);
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

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        $query = new PostQuery(get_called_class());
        if (!self::$bypassDeleteFilter) {
            $query->andWhere(['post.is_deleted' => 0]);
        }
        return $query;
    }

    public static function findWithDeleted()
    {
        return new PostQuery(get_called_class());
    }
}
