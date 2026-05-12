<?php

namespace app\behaviors;

use yii\behaviors\SluggableBehavior;

class SlugBehavior extends SluggableBehavior
{

    public $attribute = 'name';

    public $slugAttribute = 'slug';

    public $ensureUnique = true;

    public $immutable = false;
}
