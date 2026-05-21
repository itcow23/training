<?php

namespace app\models\forms;

use app\models\forms\BaseForm;

class OrderForm extends BaseForm
{

    public $account_id;
    public $membership_level_id;
    public $shipping_name;
    public $shipping_email;
    public $shipping_phone;
    public $shipping_address;
    public $discount;
    public $subtotal;
    public $shipping_fee;
    public $final_total;
    public $pay_method;
    public $status;
    public $products;



    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['account_id', 'membership_level_id', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'discount', 'subtotal', 'shipping_fee', 'final_total', 'pay_method', 'status', 'products'];
        $scenarios[self::SCENARIO_UPDATE] = ['status'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['account_id', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'pay_method', 'products'], 'required'],
            [['account_id', 'membership_level_id', 'pay_method', 'status'], 'integer'],
            [['discount', 'subtotal', 'shipping_fee', 'final_total'], 'number'],
            [['discount', 'subtotal', 'shipping_fee', 'final_total'], 'default', 'value' => 0],
            [['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => 1],
            ['products', 'validateProducts', 'on' => self::SCENARIO_CREATE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'account_id' => 'Account',
            'membership_level_id' => 'Membership level',
            'shipping_name' => 'Name',
            'shipping_email' => 'Email',
            'shipping_phone' => 'Phone',
            'shipping_address' => 'Address',
            'discount' => 'Discount amount',
            'subtotal' => 'Subtotal',
            'shipping_fee' => 'Shipping fee',
            'final_total' => 'Final total',
            'pay_method' => 'Payment method',
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
