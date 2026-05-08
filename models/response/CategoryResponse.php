<?php

namespace app\models\response;

use app\models\Category;

class CategoryResponse extends Category
{
   public function fields()
    {
        return [
            'id',
            'name',
            'products' => function ($model) {
                return array_map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                    ];
                }, array_slice($model->products, 0, 1));
            },
        ];
   }
}
