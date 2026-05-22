<?php

namespace app\helpers;

class AttributeHelper
{
    public static function filter(array $attributes): array
    {
        return array_filter(
            $attributes,
            fn($v) => !in_array($v, [null, '', [], ['']], true)
        );
    }
}
