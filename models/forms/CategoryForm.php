<?php

namespace app\models\forms;

use app\models\base\BaseCategory;
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
        $model = $this;
        return array_merge(parent::rules(), [
            [['status', 'is_deleted'], 'in', 'range' => [0, 1]],
            [['name'], 'unique', 'filter' => function ($query) use ($model) {
                if (!$model->isNewRecord) {
                    $query->andWhere(['not', ['id' => $model->id]]);
                }
                $query->withDeleted();
            }],
            [['slug'], 'unique', 'filter' => function ($query) use ($model) {
                if (!$model->isNewRecord) {
                    $query->andWhere(['not', ['id' => $model->id]]);
                }
                $query->withDeleted();
            }],
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
