<?php

namespace app\models\forms;

use app\models\Category;
use app\models\forms\BaseForm;

class CategoryForm extends BaseForm
{

    public $id;
    public $name;
    public $image;
    public $removed_image;
    public $status;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['id', 'name', 'image', 'removed_image'];
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['id', 'status'];
        return $scenarios;
    }

    public function rules()
    {
        $base = [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],
        ];

        $rules = array_merge(
            $base,
            $this->uniqueNameRule('name', Category::class),
            $this->imageRules('image', 10),
            $this->removedImageRules('removed_image')
        );

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'image' => 'Image',
            'removed_image' => 'Remove images',
        ];
    }
}
