<?php

namespace app\models\forms;

use yii\base\Model;

class ProductForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $id;
    public $category_id;
    public $name;
    public $price;
    public $status;
    public $description;
    public $discount;
    public $image;
    public $removed_image;

    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['category_id', 'name', 'price', 'status', 'description', 'discount','image'];
        $scenarios[self::SCENARIO_UPDATE] = ['category_id', 'name', 'price', 'status', 'description', 'discount','image', 'removed_image'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['category_id', 'name', 'price'], 'required'],
            [['category_id', 'status', 'discount'], 'integer'],
            [['price'], 'number'],
            [['description'], 'string'],
            [['status'], 'default', 'value' => 1],
            [['image'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['removed_image'], 'safe', 'on' => self::SCENARIO_UPDATE],
            [['removed_image'], 'each', 'rule' => ['integer']],
        ];
    }
}
