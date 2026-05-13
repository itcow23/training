<?php

namespace app\models\response;

use app\models\Order;
use Override;

class OrderResponse extends Order
{
    #[Override]
    public function fields()
    {
        return [
            'id',
            'account_id',
            'membership_level_id',
            'name',
            'email',
            'phone',
            'address',
            'discount_amount',
            'subtotal',
            'final_total',
            'pay',
            'status',
            'created_at',
            'detail' => function ($model) {
                return array_map(function ($orderDetail) {
                    $product = $orderDetail->product;
                    return [
                        'order_id' => $orderDetail->order_id,
                        'product' =>
                            [
                                'name' => $product->name,
                                'price' => $product->price,
                                'quantity' => $orderDetail->quantity,
                            ],
                        'total_price' => $orderDetail->total_price
                    ];
                }, $model->orderDetails);
            }
        ];
    }
}
