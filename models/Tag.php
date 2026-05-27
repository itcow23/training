<?php

namespace app\models;

use app\behaviors\BypassSoftDeleteBehavior;
use app\behaviors\SoftDeleteBehavior;
use app\models\query\TagQuery;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property PostTag[] $postTags
 */
class Tag extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    public static $bypassDeleteFilter = false;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name'];
        $scenarios[self::SCENARIO_UPDATE] = ['name'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name'], 'string', 'min' => 1, 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
        ];
    }

    public function behaviors()
    {
        return [
            'bypassSoftDelete' => [
                'class' => BypassSoftDeleteBehavior::class,
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'slug' => [
                'class' => SluggableBehavior::class,
                'ensureUnique' => true,
                'immutable' => false,
                'attribute' => 'name'
            ],
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }


    /**
     * Gets query for [[PostTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostTags()
    {
        return $this->hasMany(PostTag::class, ['tag_id' => 'id']);
    }

    public static function find()
    {
        $query = new TagQuery(get_called_class());
        if (!self::$bypassDeleteFilter) {
            $query->andWhere(['tag.is_deleted' => 0]);
        }
        return $query;
    }

    public static function findWithDeleted()
    {
        return new TagQuery(get_called_class());
    }
}

