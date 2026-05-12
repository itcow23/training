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
                return $model->products;
            },
            'media' => function ($model) {
                return array_map(function ($media) {
                    return [
                        'file_id' => $media->file_id,
                        'file_type' => $media->file_type,
                        'filepath' => $media->filepath,
                    ];
                }, $model->media);
            },
        ];
    }
}
