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
            'items' => function ($model) {
                return array_map(function ($orderItem) {
                    return [
                        'product_id' => $orderItem->product_id,
                        'product_name' => $orderItem->product->name ?? null,
                        'unit_price' => (float)$orderItem->unit_price,
                        'quantity' => (int)$orderItem->quantity,
                        'total_price' => (float)$orderItem->total_price
                    ];
                }, $model->orderItems);
            }
        ];
    }
}
