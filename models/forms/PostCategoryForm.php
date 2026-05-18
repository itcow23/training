<?php

namespace app\models\forms;

use app\models\PostCategory;
use yii\base\Model;

class PostCategoryForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $id;
    public $name;

    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name'];
        $scenarios[self::SCENARIO_UPDATE] = ['id', 'name'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            ['name', 'unique', 'targetClass' => PostCategory::class, 'targetAttribute' => 'name', 'filter' => function ($query) {
                if ($this->id) {
                    $query->andWhere(['<>', 'id', $this->id]);
                }
            }],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
        ];
    }
}
