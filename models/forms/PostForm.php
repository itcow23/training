<?php

namespace app\models\forms;


class PostForm extends BaseForm
{
    public $title;
    public $description;
    public $content;
    public $published_at;
    public $thumbnail;
    public $status;
    public $category_id;
    public $image;
    public $removed_image;
    public $add_tag;
    public $removed_tag;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['title', 'description', 'content', 'published_at', 'thumbnail', 'status', 'category_id', 'image', 'add_tag'];
        $scenarios[self::SCENARIO_UPDATE] = ['title', 'description', 'content', 'published_at', 'thumbnail', 'status', 'category_id', 'image', 'removed_image', 'add_tag', 'removed_tag'];
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        return $scenarios;
    }

    public function rules()
    {

        $base = [
            [['title', 'content', 'category_id'], 'required', 'on' => self::SCENARIO_CREATE],
            [['description', 'content'], 'string'],
            [['published_at'], 'safe'],
            [['status', 'category_id'], 'integer'],
            [['title', 'thumbnail'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 0],
            [['removed_image', 'add_tag', 'removed_tag'], 'validateArray'],
            [['add_tag', 'removed_tag'], 'each', 'rule' => ['integer']],
        ];

        $rules = array_merge(
            $base,
            $this->imageRules('image', 10),
            $this->removedImageRules('removed_image')
        );

        return $rules;
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
            'add_tag' => 'Add tags',
            'removed_tag' => 'Remove tags',
        ];
    }
}
