<?php
namespace app\behaviors;

use yii\behaviors\TimestampBehavior;

class DateTimeBehavior extends TimestampBehavior
{
    public $value;

    public function init()
    {
        parent::init();

        $this->value ??= fn () => date('Y-m-d H:i:s');
    }
}
