<?php

namespace app\services;

use app\models\OrderDetail;
use yii\web\HttpException;

class OrderDetailService
{
    public function create($orderId, $productList, $products)
    {
        foreach ($products as $item) {
            $product = $productList[$item['product_id']];

            if (!isset($product)) {
                throw new HttpException(404, "Product ID {$item['product_id']} not found");
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
