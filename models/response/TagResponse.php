<?php

namespace app\models\response;

use app\models\Tag;

class TagResponse extends Tag
{
    public function fields()
    {
        return [
            'id',
            'name'
        ];
    }
}
