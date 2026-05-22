<?php

namespace app\models;

use app\behaviors\MediaBehavior;
use app\behaviors\SlugBehavior;
use app\models\query\ProductQuery;
use Override;
use yii\behaviors\TimestampBehavior;

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
 * @property Gallery[] $galleries
 * @property OrderItem[] $orderItems
 * @property PostProduct[] $postProducts
 */
class Product extends \yii\db\ActiveRecord
{

    public $image;
    public $removed_image;

    #[Override]
    public function behaviors()
    {
        return [
            SlugBehavior::class,
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'media' => [
                'class' => MediaBehavior::class,
                'collection' => 'gallery',
            ],
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
     * Gets query for [[Galleries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGalleries()
    {
        return $this->hasMany(Gallery::class, ['product_id' => 'id']);
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

    public function getMedia()
    {
        return $this->hasMany(Media::class, ['file_id' => 'id'])->andWhere(['file_type' => 'product']);
    }

    public static function find()
    {
        return new ProductQuery(get_called_class());
    }
}
