<?php

namespace app\services;

use app\models\OrderDetail;
use app\models\Product;
use yii\web\HttpException;

class OrderDetailService
{
    public function create($orderId, $products)
    {
        foreach ($products as $item) {

            $product = Product::findOne($item['product_id']);

            if (!$product) {
                throw new HttpException(404, 'Product not found');
            }

            $detail = new OrderDetail();

            $detail->order_id = $orderId;
            $detail->product_id = $product->id;
            $detail->quantity = $item['quantity'];

            $detail->total_price = $product->price * $item['quantity'];

            if (!$detail->save()) {
                throw new HttpException(
                    422,
                    json_encode($detail->errors)
                );
            }
        }
    }
}
