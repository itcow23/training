<?php

namespace app\models\base;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 */
class BaseTag extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['name'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['is_deleted'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'deleted_at' => 'Deleted At',
        ];
    }
}
