<?php

namespace app\models\response;

use app\models\PostCategory;

class PostCategoryResponse extends PostCategory
{
    public function fields()
    {
        return [
            'id',
            'name',
            'posts' => function ($model) {
                return array_map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'description' => $post->description,
                        'content' => $post->content,
                        'status' => $post->status,
                        'published_at' => $post->published_at
                    ];
                }, $model->posts);
            },
        ];
    }
}
