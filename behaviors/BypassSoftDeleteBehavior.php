<?php

namespace app\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * BypassSoftDeleteBehavior automates the setting and resetting of the static
 * `$bypassDeleteFilter` flag during ActiveRecord validation lifecycle events.
 * This ensures soft-deleted records are included in uniqueness/slug checks,
 * eliminating duplicate database constraint crashes while keeping normal queries filtered.
 */
class BypassSoftDeleteBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'enableBypass',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'disableBypass',
        ];
    }

    public function enableBypass()
    {
        $modelClass = get_class($this->owner);
        if (property_exists($modelClass, 'bypassDeleteFilter')) {
            $modelClass::$bypassDeleteFilter = true;
        }
    }

    public function disableBypass()
    {
        $modelClass = get_class($this->owner);
        if (property_exists($modelClass, 'bypassDeleteFilter')) {
            $modelClass::$bypassDeleteFilter = false;
        }
    }
}
