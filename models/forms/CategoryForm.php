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
        $scenarios[self::SCENARIO_UPDATE] = ['name','status', 'image', 'removed_image'];
        $scenarios[self::SCENARIO_UPDATE_STATUS] = ['status'];
        return $scenarios;
    }

    public function rules()
    {
        $base = [
            [['name'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['name'], 'validateOnUpdate', 'on' => [self::SCENARIO_UPDATE], 'skipOnEmpty' => false],
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
