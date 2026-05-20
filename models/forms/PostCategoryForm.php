<?php

namespace app\models\forms;

use app\models\PostCategory;
use app\models\forms\BaseForm;

class PostCategoryForm extends BaseForm
{

    public $id;
    public $name;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name'];
        $scenarios[self::SCENARIO_UPDATE] = ['id', 'name'];
        return $scenarios;
    }

    public function rules()
    {
        $base = [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];

        $rules = array_merge(
            $base,
            $this->uniqueNameRule('name', PostCategory::class)
        );

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
        ];
    }
}
