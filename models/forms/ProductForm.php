<?php

namespace app\models\forms;

use app\models\Product;
use yii\web\UploadedFile;

class ProductForm extends Product
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
            [['status'], 'in', 'range' => [0, 1]],
            [['price'], 'number', 'min' => 0],
            [['discount'], 'number', 'min' => 0, 'max' => 100],
            [['slug'], 'unique', 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
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
            [['removed_image'], 'each', 'rule' => ['integer'], 'when' => fn($model) => !$model->isNewRecord],
        ]);
    }
}
