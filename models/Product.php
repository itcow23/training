<?php

namespace app\models;

use app\behaviors\MediaBehavior;
use app\behaviors\SoftDeleteBehavior;
use app\models\base\BaseProduct;
use app\models\query\ProductQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

class Product extends BaseProduct
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

    public function fields()
    {
        return [
            'id',
            'category_id',
            'name',
            'price',
            'status',
            'description',
            'discount',
            'category' => function ($model) {
                return $model->category ? $model->category->name : null;
            },
            'media' => function ($model) {
                return array_map(function ($media) {
                    return [
                        'id' => $media->id,
                        'filepath' => $media->filepath
                    ];
                }, $model->media);
            },
        ];
    }

    public function extraFields()
    {
        return [
            'posts',
        ];
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
        return new ProductQuery(get_called_class());
    }
}
