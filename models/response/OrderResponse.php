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
            'order_code',
            'account_id',
            'membership_level_id',
            'discount',
            'subtotal',
            'shipping_fee',
            'final_total',
            'pay_method',
            'status',
            'created_at',
            'detail' => function ($model) {
                return array_map(function ($orderItem) {
                    $product = $orderItem->product;
                    return [
                        'order_id' => $orderItem->order_id,
                        'product' => [
                            'name' => $product->name,
                            'unit_price' => $orderItem->unit_price,
                            'quantity' => $orderItem->quantity,
                        ],
                        'total_price' => $orderItem->total_price
                    ];
                }, $model->orderItems);
            }
        ];
    }
}
