<?php

namespace app\models\forms;

use yii\base\Model;

class OrderForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $account_id;
    public $membership_level_id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $discount_amount;
    public $subtotal;
    public $final_total;
    public $pay;
    public $status;
    public $products;



    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['account_id', 'membership_level_id', 'name', 'email', 'phone', 'address', 'discount_amount', 'subtotal', 'final_total', 'pay', 'status','products'];
        $scenarios[self::SCENARIO_UPDATE] = ['status'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['account_id', 'name', 'email', 'phone', 'address', 'pay','products'], 'required'],
            [['account_id', 'membership_level_id', 'pay', 'status'], 'integer'],
            [['discount_amount', 'subtotal', 'final_total'], 'number'],
            [['name', 'email', 'phone', 'address'], 'string', 'max' => 255],
            [['discount_amount', 'subtotal', 'final_total'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],
            ['products', 'required', 'on' => self::SCENARIO_CREATE],
            ['products', 'each', 'rule' => ['safe'], 'on' => self::SCENARIO_CREATE],

        ];
    }

}
