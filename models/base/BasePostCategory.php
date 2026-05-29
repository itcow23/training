<?php

namespace app\models\base;

use app\models\query\PostCategoryQuery;
use Yii;

/**
 * This is the model class for table "post_category".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $status
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 */
class BasePostCategory extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['is_deleted'], 'default', 'value' => 0],
            [['name', 'slug'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['status', 'is_deleted'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
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
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'deleted_at' => 'Deleted At',
        ];
    }

     public static function find()
    {
        return new PostCategoryQuery(get_called_class());
    }
}
