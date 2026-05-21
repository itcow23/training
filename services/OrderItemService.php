<?php

namespace app\services;

use app\models\OrderItem;
use RuntimeException;

class OrderItemService
{
    public function create($orderId, $productList, $products)
    {
        foreach ($products as $item) {
            $product = $productList[$item['product_id']];

            if (!isset($product)) {
                throw new RuntimeException("Product ID {$item['product_id']} does not exist.");
            }

            $detail = new OrderItem();

            $detail->order_id = $orderId;
            $detail->product_id = $product->id;
            $detail->quantity = $item['quantity'];
            $detail->unit_price = $product->price;
            $detail->total_price = $product->price * $item['quantity'];

            if (!$detail->save()) {
                throw new RuntimeException(json_encode($detail->errors));
            }
        }
    }
}
