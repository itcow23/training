<?php

namespace app\models;

use app\behaviors\BypassSoftDeleteBehavior;
use app\behaviors\MediaBehavior;
use app\behaviors\SoftDeleteBehavior;
use app\models\query\ProductQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property float $price
 * @property int|null $status
 * @property string|null $description
 * @property int|null $discount
 * @property string|null $slug
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property CartItem[] $cartItems
 * @property Category $category
 * @property OrderItem[] $orderItems
 * @property PostProduct[] $postProducts
 */
class Product extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    public $image;
    public $removed_image;
    public static $bypassDeleteFilter = false;

    public function behaviors()
    {
        return [
            'bypassSoftDelete' => [
                'class' => BypassSoftDeleteBehavior::class,
            ],
            'slug' => [
                'class' => SluggableBehavior::class,
                'ensureUnique' => true,
                'immutable' => false,
                'attribute' => 'name'
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'media' => [
                'class' => MediaBehavior::class,
                'collection' => 'gallery',
                'folder' => 'product',
            ],
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
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
        $scenarios[self::SCENARIO_CREATE] = ['category_id', 'name', 'price', 'description', 'discount', 'status', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['category_id', 'name', 'price', 'description', 'discount', 'status', 'image', 'removed_image'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['category_id', 'name', 'price'], 'required'],
            [['category_id'], 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'id'],
            [['category_id', 'status', 'discount'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [0, 1]],
            [['name'], 'string', 'max' => 255],
            [['price'], 'number', 'min' => 0],
            [['description'], 'string'],
            [['discount'], 'integer', 'min' => 0, 'max' => 100],

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
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'price',
            'status',
            'description',
            'discount',
            'category' => function ($model) {
                return $model->category ? $model->category->name : null;
            },
            'media' => function ($model) {
                return array_column($model->media, 'filepath');
            },
            'posts' => function ($model) {
                return array_map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'published_at' => $post->published_at
                    ];
                }, $model->posts);
            }
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * Gets query for [[CartItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(CartItem::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }


    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[PostProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostProducts()
    {
        return $this->hasMany(PostProduct::class, ['product_id' => 'id']);
    }

    public function getPosts()
    {
        return $this->hasMany(Post::class, ['id' => 'post_id'])->viaTable('post_product', ['product_id' => 'id']);
    }

    public function getMedia()
    {
        return $this->hasMany(Media::class, ['file_id' => 'id'])->andWhere(['file_type' => 'product']);
    }

    public static function find()
    {
        $query = new ProductQuery(get_called_class());
        if (!self::$bypassDeleteFilter) {
            $query->andWhere(['product.is_deleted' => 0]);
        }
        return $query;
    }

    public static function findWithDeleted()
    {
        return new ProductQuery(get_called_class());
    }
}
