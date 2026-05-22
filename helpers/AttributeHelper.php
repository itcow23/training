<?php

namespace app\helpers;

use yii\base\Model;

class AttributeHelper
{
    public static function map(Model $model, Model $form, array $pushedAttributes = []): void
    {
        if (empty($pushedAttributes)) {
            $attributesToMap = $form->safeAttributes();
        } else {
            $attributesToMap = $pushedAttributes;
        }

        foreach ($attributesToMap as $name) {
            if ($name === 'id') continue;

            if ($form->hasProperty($name) && $model->canSetProperty($name) && $form->isAttributeSafe($name)) {
                $model->$name = $form->$name;
            }
        }
    }
}
