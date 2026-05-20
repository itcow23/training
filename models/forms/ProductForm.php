<?php

namespace app\models\forms;

use app\models\forms\BaseForm;

class ProductForm extends BaseForm
{


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
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['category_id', 'name', 'price', 'status', 'description', 'discount', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['category_id', 'name', 'price', 'status', 'description', 'discount', 'image', 'removed_image'];
        return $scenarios;
    }

    public function rules()
    {
        $rules = [
            [['category_id', 'name', 'price'], 'required', 'on' => self::SCENARIO_CREATE],
            [['category_id', 'status', 'discount'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['name'], 'string', 'max' => 255],
            [['price'], 'number'],
            [['description'], 'string'],
        ];


        $rules = array_merge(
            $rules,
            $this->imageRules('image', 10),
            $this->removedImageRules('removed_image')
        );



        return $rules;
    }
}
