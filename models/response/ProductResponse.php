<?php

namespace app\models\response;

use app\models\Product;
use Override;

class ProductResponse extends Product
{
    #[Override]
    public function fields()
    {
        return[
            'id',
            'name',
            'price',
            'status',
            'description',
            'discount',
            'category' => function ($model){
               return $model->category->name;
            },
            'media' => function ($model){
                return array_map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_id' => $media->file_id,
                        'file_type' => $media->file_type,
                        'path' => $media->filepath
                    ];
                },$model->media);
            }
        ];
    }
}
