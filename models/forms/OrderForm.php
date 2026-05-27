<?php

namespace app\models\forms;

use app\models\Account;
use app\models\forms\BaseForm;
use Yii;

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
            [['account_id'], 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            [['account_id', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'pay_method', 'products'], 'required'],
            [['account_id', 'membership_level_id', 'pay_method', 'status'], 'integer'],
            [['discount', 'shipping_fee',], 'number'],
            [['discount', 'shipping_fee',], 'default', 'value' => 0],
            [['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address'], 'string', 'max' => 255],
            [['shipping_email'], 'email'],
            [['status'], 'required', 'on' => self::SCENARIO_UPDATE],
            [['status'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [0, 1, 2, 3, 4]],
            [['products'], 'validateProducts', 'on' => self::SCENARIO_CREATE],
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

    public function save(\app\models\Order $model)
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->scenario === self::SCENARIO_UPDATE) {
            $newStatus = (int)$this->status;
            $oldStatus = (int)$model->status;

            if (!$this->validateStatusTransition($model, $oldStatus, $newStatus)) {
                $this->addError('status', 'Invalid status transition.');
                return false;
            }

            $model->status = $newStatus;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException(json_encode($model->errors));
                }
                $transaction->commit();
                return $model;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $this->addError('status', $e->getMessage());
                return false;
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->account_id = $this->account_id;
            $membershipLevelId = 1;
            if ($this->account_id) {
                $account = Account::findOne($this->account_id);
                if ($account && $account->membership_level_id) {
                    $membershipLevelId = $account->membership_level_id;
                }
            }
            $model->membership_level_id = $membershipLevelId;

            $model->shipping_name = $this->shipping_name;
            $model->shipping_email = $this->shipping_email;
            $model->shipping_phone = $this->shipping_phone;
            $model->shipping_address = $this->shipping_address;
            $model->pay_method = $this->pay_method;
            $model->status = $this->status ?? 1;

            if (empty($this->products)) {
                throw new \RuntimeException('Products list is required.');
            }

            $subtotal = 0;
            $productIds = array_column($this->products, 'product_id');
            $productList = \app\models\Product::find()
                ->where(['id' => $productIds])
                ->indexBy('id')
                ->all();

            foreach ($this->products as $item) {
                if (!isset($productList[$item['product_id']])) {
                    throw new \RuntimeException('Product does not exist.');
                }
                $product = $productList[$item['product_id']];
                $subtotal += $product->price * $item['quantity'];
            }

            $discountAmount = 0;
            if ($model->membership_level_id) {
                $discountRate = \app\models\MembershipLevel::find()
                    ->select('discount_rate')
                    ->where(['id' => $model->membership_level_id])
                    ->scalar();

                if ($discountRate) {
                    $discountAmount = $subtotal * ($discountRate / 100);
                }
            }

            $model->subtotal = $subtotal;
            $model->discount = $discountAmount;
            $model->shipping_fee = $this->shipping_fee ?? 0;
            $model->final_total = $subtotal - $discountAmount + $model->shipping_fee;

            if (!$model->save()) {
                throw new \RuntimeException(json_encode($model->errors));
            }

            foreach ($this->products as $item) {
                $product = $productList[$item['product_id']];
                $detail = new \app\models\OrderItem();
                $detail->order_id = $model->id;
                $detail->product_id = $product->id;
                $detail->quantity = $item['quantity'];
                $detail->unit_price = $product->price;
                $detail->total_price = $product->price * $item['quantity'];

                if (!$detail->save()) {
                    throw new \RuntimeException(json_encode($detail->errors));
                }
            }

            $transaction->commit();
            return $model;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('order', $e->getMessage());
            return false;
        }
    }

    private function validateStatusTransition($model, $oldStatus, $newStatus)
    {
        $allowedTransitions = [
            $model::STATUS_PENDING => [
                $model::STATUS_CONFIRM,
                $model::STATUS_CANCEL
            ],
            $model::STATUS_CONFIRM => [
                $model::STATUS_SHIPPING,
                $model::STATUS_CANCEL
            ],
            $model::STATUS_SHIPPING => [
                $model::STATUS_COMPLETED
            ],
            $model::STATUS_COMPLETED => [],
            $model::STATUS_CANCEL => [],
        ];

        return in_array($newStatus, $allowedTransitions[$oldStatus] ?? []);
    }
}
