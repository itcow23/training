<?php

namespace app\models\forms;

use app\models\forms\BaseForm;

class OrderForm extends BaseForm
{

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
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['account_id', 'membership_level_id', 'name', 'email', 'phone', 'address', 'discount_amount', 'subtotal', 'final_total', 'pay', 'status', 'products'];
        $scenarios[self::SCENARIO_UPDATE] = ['status'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['account_id', 'name', 'email', 'phone', 'address', 'pay', 'products'], 'required'],
            [['account_id', 'membership_level_id', 'pay', 'status'], 'integer'],
            [['discount_amount', 'subtotal', 'final_total'], 'number'],
            [['discount_amount', 'subtotal', 'final_total'], 'default', 'value' => 0],
            [['name', 'email', 'phone', 'address'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 1],
            ['products', 'validateProducts', 'on' => self::SCENARIO_CREATE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'account_id' => 'Account',
            'membership_level_id' => 'Membership level',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'discount_amount' => 'Discount amount',
            'subtotal' => 'Subtotal',
            'final_total' => 'Final total',
            'pay' => 'Payment method',
            'status' => 'Status',
            'products' => 'Products',
        ];
    }

    public function validateProducts(string $attribute): void
    {
        if (!is_array($this->products) || $this->products === []) {
            $this->addError($attribute, 'Products must be a non-empty array.');

            return;
        }

        foreach ($this->products as $i => $row) {
            if (!is_array($row)) {
                $this->addError($attribute, "Row {$i}: invalid item (expected an object/array).");

                continue;
            }
            if (!isset($row['product_id'], $row['quantity'])) {
                $this->addError($attribute, "Row {$i}: missing product_id or quantity.");

                continue;
            }
            if (!is_numeric($row['product_id']) || (int) $row['product_id'] < 1) {
                $this->addError($attribute, "Row {$i}: invalid product_id.");

                continue;
            }
            if (!is_numeric($row['quantity']) || (int) $row['quantity'] < 1) {
                $this->addError($attribute, "Row {$i}: quantity must be a positive integer.");

                continue;
            }
        }
    }
}
