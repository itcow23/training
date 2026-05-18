<?php

namespace app\models\forms;

use yii\base\Model;

class PostForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $title;
    public $description;
    public $content;
    public $published_at;
    public $thumbnail;
    public $status;
    public $slug;
    public $category_id;
    public $image;
    public $removed_image;

    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['title', 'description', 'content', 'published_at', 'thumbnail', 'status', 'category_id', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['title', 'description', 'content', 'published_at', 'thumbnail', 'status', 'category_id', 'image', 'removed_image'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['title', 'content', 'slug', 'category_id'], 'required'],
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
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'content' => 'Nội dung',
            'published_at' => 'Ngày xuất bản',
            'thumbnail' => 'Ảnh đại diện',
            'status' => 'Trạng thái',
            'category_id' => 'Danh mục bài viết',
            'image' => 'Ảnh bài viết',
            'removed_image' => 'Xóa ảnh',
        ];
    }
}
