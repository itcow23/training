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

        if (!$model->save(false)) {
            $model->addError($this->deletedAttribute, 'Could not soft delete the record.');
            return false;
        }

        return true;
    }

    public function restore(): bool
    {
        $model = $this->owner;

        $oldDeleted = $model->getOldAttribute($this->deletedAttribute);
        $oldDeletedAt = $this->deletedAtAttribute ? $model->getOldAttribute($this->deletedAtAttribute) : null;

        $model->{$this->deletedAttribute} = 0;
        if ($this->deletedAtAttribute) {
            $model->{$this->deletedAtAttribute} = null;
        }

        if (!$model->validate()) {
            $model->{$this->deletedAttribute} = $oldDeleted;
            if ($this->deletedAtAttribute) {
                $model->{$this->deletedAtAttribute} = $oldDeletedAt;
            }
            return false;
        }

        if (!$model->save(false)) {
            $model->addError($this->deletedAttribute, 'Could not restore the record.');
            return false;
        }

        return true;
    }

    public function handleBeforeDelete($event)
    {
        $event->isValid = false;
        $this->softDelete();
    }
}
