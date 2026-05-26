<?php

namespace app\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class SoftDeleteBehavior extends Behavior
{
    public string $deletedAttribute = 'is_deleted';
    public string $deletedAtAttribute = 'deleted_at';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'handleBeforeDelete',
        ];
    }

    public function softDelete(): bool
    {
        $model = $this->owner;

        $model->{$this->deletedAttribute} = 1;
        if ($this->deletedAtAttribute) {
            $model->{$this->deletedAtAttribute} = date('Y-m-d H:i:s');
        }

        return $model->save(false);
    }

    public function restore(): bool
    {
        $model = $this->owner;

        $model->{$this->deletedAttribute} = 0;
        if ($this->deletedAtAttribute) {
            $model->{$this->deletedAtAttribute} = null;
        }

        return $model->save(false);
    }

    public function handleBeforeDelete($event)
    {
        $event->isValid = false;
        $this->softDelete();
    }
}
