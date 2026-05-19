<?php

namespace app\models\forms;

use yii\base\Model;

class PostForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_STATUS = 'update_status';
    public $title;
    public $description;
    public $content;
    public $published_at;
    public $thumbnail;
    public $status;
    public $category_id;
    public $image;
    public $removed_image;

    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['title', 'description', 'content', 'published_at', 'thumbnail', 'status', 'category_id', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['title', 'description', 'content', 'published_at', 'thumbnail', 'status', 'category_id', 'image', 'removed_image'];
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['title', 'content', 'category_id'], 'required'],
            [['description', 'content'], 'string'],
            [['published_at'], 'safe'],
            [['status', 'category_id'], 'integer'],
            [['title', 'thumbnail'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 0],
            [['image'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['removed_image'], 'safe', 'on' => self::SCENARIO_UPDATE],
            [['removed_image'], 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'published_at' => 'Published at',
            'thumbnail' => 'Thumbnail',
            'status' => 'Status',
            'category_id' => 'Category',
            'image' => 'Images',
            'removed_image' => 'Remove images',
        ];
    }
}
