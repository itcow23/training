<?php

namespace app\models\response;

use app\models\Post;
use Override;

class PostResponese extends Post
{
    #[Override]
    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'description',
            'content',
            'status',
            'published_at',
            'media' => function ($model) {
                return array_map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_id' => $media->file_id,
                        'file_type' => $media->file_type,
                        'path' => $media->filepath
                    ];
                }, $model->media);
            }
        ];
    }
}
