<?php

namespace app\models;

use app\behaviors\DateTimeBehavior;
use app\behaviors\MediaBehavior;
use app\behaviors\SlugBehavior;
use app\behaviors\SoftDeleteBehavior;
use app\models\base\BaseCategory;
use app\models\Product;
use app\models\Media;
use app\models\query\CategoryQuery;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Product[] $products
 */
class Category extends BaseCategory
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SlugBehavior::class,
                'uniqueValidator' => [
                    'targetClass' => BaseCategory::class,
                ],
            ],
            'timestamp' => [
                'class' => DateTimeBehavior::class,
            ],
            'media' => [
                'class' => MediaBehavior::class,
                'collection' => 'thumbnail',
                'folder' => 'category',
            ],
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert && isset($changedAttributes['is_deleted']) && $this->is_deleted == 1) {
            Product::updateAll(
                ['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')],
                ['category_id' => $this->id, 'is_deleted' => 0]
            );
        }
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'status',
            'media' => function ($model) {
                return array_column($model->media, 'filepath');
            },
        ];
    }

    public function extraFields()
    {
        return [
            'products',
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id']);
    }

    /**
     * Gets query for [[Media]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::class, ['file_id' => 'id'])->andWhere(['file_type' => 'category']);
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
}
