<?php

namespace app\models;

use app\behaviors\MediaBehavior;
use app\models\query\CategoryQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

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
class Category extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_STATUS = 'update_status';

    public $image;
    public $removed_image;
    public static $bypassDeleteFilter = false;

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->image = UploadedFile::getInstancesByName('image');
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SluggableBehavior::class,
                'ensureUnique' => true,
                'immutable' => false,
                'attribute' => 'name'
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
            'media' => [
                'class' => MediaBehavior::class,
                'collection' => 'thumbnail',
                'folder' => 'category',
            ],
            'softDelete' => [
                'class' => \app\behaviors\SoftDeleteBehavior::class,
            ],
            'bypassSoftDelete' => [
                'class' => \app\behaviors\BypassSoftDeleteBehavior::class,
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'status', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['name', 'status', 'image', 'removed_image'];
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => [0, 1]],
            [['status'], 'default', 'value' => 1],
            [
                ['image'],
                'file',
                'skipOnEmpty' => true,
                'maxFiles' => 10,
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'maxSize' => 5 * 1024 * 1024,
            ],
            [['removed_image'], 'each', 'rule' => ['integer']],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'status',
            'products' => function ($model) {
                return $model->products;
            },
            'media' => function ($model) {
                return array_map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_id' => $media->file_id,
                        'file_type' => $media->file_type,
                        'filepath' => $media->filepath,
                    ];
                }, $model->media);
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
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

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
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

    public static function find()
    {
        $query = new CategoryQuery(get_called_class());
        if (!self::$bypassDeleteFilter) {
            $query->andWhere(['category.is_deleted' => 0]);
        }
        return $query;
    }

    public static function findWithDeleted()
    {
        return new CategoryQuery(get_called_class());
    }
}
