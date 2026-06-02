<?php

namespace app\models\base;

use app\models\Order;
use app\models\Product;

/**
 * This is the model class for table "order_items".
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $product_name
 *
 * @property Order $order
 * @property Product $product
 */
class BaseOrderItem extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'product_name'], 'default', 'value' => null],
            [['order_id', 'product_id', 'quantity', 'unit_price', 'total_price'], 'required'],
            [['order_id', 'product_id', 'quantity'], 'integer'],
            [['unit_price', 'total_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['product_name'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'product_name' => 'Product Name',
        ];
    }
}
