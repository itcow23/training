<?php

namespace app\models\forms;

use app\models\Category;
use yii\base\Model;

class CategoryForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $id;
    public $name;
    public $image;
    public $removed_image;

    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['id', 'name', 'image', 'removed_image'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            ['name', 'unique', 'targetClass' => Category::class, 'targetAttribute' => 'name', 'filter' => function ($query) {
                if ($this->id) {
                    $query->andWhere(['!=', 'id', $this->id]);
                }
            }],
            [['name'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['removed_image'], 'safe', 'on' => self::SCENARIO_UPDATE],
            [['removed_image'], 'each', 'rule' => ['integer']],
        ];
    }

    // public function attributeLabels()
    // {
    //     return [
    //         'id' => 'ID',
    //         'name' => 'Tên danh mục',
    //         'slug' => 'Slug',
    //         'image' => 'Ảnh danh mục',
    //         'removed_image' => 'Xóa ảnh',
    //     ];
    // }
}
