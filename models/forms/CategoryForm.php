<?php

namespace app\models\forms;

use app\models\Category;
use yii\web\UploadedFile;

class CategoryForm extends Category
{
    public $image;
    public $removed_image;

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->image = UploadedFile::getInstancesByName('image');
            if ($this->isNewRecord) {
                $this->removed_image = null;
            }
            return true;
        }
        return false;
    }
    public function rules()
    {
        return array_merge(parent::rules(), [
           [
                ['image'],
                'file',
                'skipOnEmpty' => true,
                'maxFiles' => 10,
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'maxSize' => 5 * 1024 * 1024,
            ],
            [
                ['removed_image'],
                'each',
                'rule' => ['integer'],
                'when' => fn($model) => !$model->isNewRecord,
            ],
        ]);
    }
}
