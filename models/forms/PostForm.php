<?php

namespace app\models\forms;

use app\models\Post;
use app\models\PostProduct;
use app\models\PostTag;
use app\models\Product;
use app\models\Tag;
use Yii;
use yii\web\UploadedFile;

class PostForm extends Post
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_HIDDEN = 2;
    const STATUS_ARCHIVED = 3;

    public $image;
    public $removed_image;
    public $add_tag;
    public $add_product;


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
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_HIDDEN, self::STATUS_ARCHIVED]],
            [['slug'], 'unique', 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
                return $query->withDeleted();
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
            [['add_tag', 'add_product'], 'each', 'rule' => ['integer']],
            [
                ['add_tag'],
                'each',
                'rule' => [
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => Tag::class,
                    'targetAttribute' => ['add_tag' => 'id'],
                    'filter' => fn($query) => $query->notDeleted(),
                ]
            ],
            [
                ['add_product'],
                'each',
                'rule' => [
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => Product::class,
                    'targetAttribute' => ['add_product' => 'id'],
                    'filter' => fn($query) => $query->notDeleted(),
                ]
            ],
        ]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ((int)$this->status === self::STATUS_PUBLISHED) {
                if ($insert) {
                    $this->published_at = date('Y-m-d H:i:s');
                } else {
                    $oldPublishedAt = $this->getOldAttribute('published_at');
                    if (empty($oldPublishedAt)) {
                        $this->published_at = date('Y-m-d H:i:s');
                    } else {
                        $this->published_at = $oldPublishedAt;
                    }
                }
            } else {
                if ($insert) {
                    $this->published_at = null;
                } else {
                    $oldPublishedAt = $this->getOldAttribute('published_at');
                    if (!empty($oldPublishedAt)) {
                        $this->published_at = $oldPublishedAt;
                    } else {
                        $this->published_at = null;
                    }
                }
            }
            return true;
        }
        return false;
    }



    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->syncRelation(PostTag::class, 'tag_id', $this->add_tag);

        $this->syncRelation(PostProduct::class, 'product_id', $this->add_product);
    }

    protected function syncRelation(string $modelClass, string $fkTargetColumn, ?array $newIds)
    {
        if ($newIds === null) {
            return;
        }

        $existingIds = $modelClass::find()
            ->select([$fkTargetColumn])
            ->where(['post_id' => $this->id])
            ->column();

        $toDeleteIds = array_diff($existingIds, $newIds);
        if (!empty($toDeleteIds)) {
            $modelClass::deleteAll(['post_id' => $this->id, $fkTargetColumn => $toDeleteIds]);
        }

        $toAddIds = array_diff($newIds, $existingIds);
        if (!empty($toAddIds)) {
            $rows = [];
            $tableName = $modelClass::tableName();
            $hasCreatedAt = $modelClass::getTableSchema()->getColumn('created_at') !== null;

            foreach ($toAddIds as $targetId) {
                if ($hasCreatedAt) {
                    $rows[] = [$this->id, $targetId, date('Y-m-d H:i:s')];
                } else {
                    $rows[] = [$this->id, $targetId];
                }
            }

            if (!empty($rows)) {
                $columns = $hasCreatedAt ? ['post_id', $fkTargetColumn, 'created_at'] : ['post_id', $fkTargetColumn];
                Yii::$app->db->createCommand()
                    ->batchInsert($tableName, $columns, $rows)
                    ->execute();
            }
        }
    }
}
