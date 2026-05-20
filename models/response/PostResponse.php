<?php

namespace app\models\response;

use app\models\Post;
use Override;

class PostResponse extends Post
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
            'comment' => function ($model) {
                return count($model->comments);
            },
            'rating' => function ($model) {
                return round(
                    array_sum(array_column($model->ratings, 'score'))
                        / (count($model->ratings) ?: 1),
                    1
                );
            },
            'media' => function ($model) {
                return array_map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_id' => $media->file_id,
                        'file_type' => $media->file_type,
                        'path' => $media->filepath
                    ];
                }, $model->media);
            },
            'tags' => function ($model) {
                return array_map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name
                    ];
                }, $model->tags);
            },
        ];
    }
}
