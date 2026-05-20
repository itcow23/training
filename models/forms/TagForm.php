<?php

namespace app\models\forms;

use app\models\Tag;
use app\models\forms\BaseForm;

class TagForm extends BaseForm
{

    public $id;
    public $name;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name'];
        $scenarios[self::SCENARIO_UPDATE] = ['name'];
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
            $this->uniqueNameRule('name', Tag::class)
        );

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
        ];
    }
}
