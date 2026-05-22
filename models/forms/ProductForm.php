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
        $scenarios[self::SCENARIO_CREATE] = ['category_id', 'name', 'price', 'description', 'discount', 'status', 'image'];
        $scenarios[self::SCENARIO_UPDATE] = ['category_id', 'name', 'price', 'status', 'description', 'discount', 'image', 'removed_image'];
        return $scenarios;
    }

    public function rules()
    {
        $rules = [
            [['category_id'], 'exist', 'targetClass' => \app\models\Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['category_id', 'name', 'price'], 'required', 'on' => self::SCENARIO_CREATE],
            [['category_id', 'name', 'price'], 'validateOnUpdate', 'on' => self::SCENARIO_UPDATE, 'skipOnEmpty' => false],
            [['category_id', 'status'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [0, 1]],
            [['name'], 'string', 'max' => 255],
            [['price'], 'number', 'min' => 0],
            [['description'], 'string'],
            [['discount'], 'integer', 'min' => 0, 'max' => 100],
        ];


        $rules = array_merge(
            $rules,
            $this->imageRules('image', 10),
            $this->removedImageRules('removed_image')
        );



        return $rules;
    }
}
