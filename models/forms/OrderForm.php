<?php

namespace app\models\forms;

use app\models\Account;
use app\models\Product;
use app\models\Order;
use app\models\OrderItem;
use yii\base\Model;
use Yii;

class OrderForm extends Model
{
    public $id;
    public $account_id;
    public $shipping_name;
    public $shipping_email;
    public $shipping_phone;
    public $shipping_address;
    public $pay_method;
    public $status;
    public $products;

    private $_accountModel = null;
    private $_productModels = [];

    public function rules()
    {
        return [
            [['account_id'], 'integer'],
            [['account_id'], 'required'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id'], 'filter' => ['status' => 1]],
            [['shipping_name', 'shipping_phone', 'shipping_address'], 'required'],
            [['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address'], 'string', 'max' => 255],
            [['shipping_email'], 'email'],
            [['status', 'pay_method'], 'integer'],
            [['status', 'pay_method'], 'default', 'value' => 1],
            [['status'], 'in', 'range' => [
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRM,
                Order::STATUS_SHIPPING,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCEL
            ]],
            [['pay_method'], 'in', 'range' => [
                Order::PAY_METHOD_COD,
                Order::PAY_METHOD_BANK_TRANSFER,
                Order::PAY_METHOD_CREDIT_CARD
            ]],
            [['products'], 'required'],
            [['products'], 'validateProducts']
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->normalizeProducts();
            return true;
        }
        return false;
    }

    protected function normalizeProducts()
    {
        if (is_array($this->products)) {
            $merged = [];
            foreach ($this->products as $product) {
                if (is_array($product) && isset($product['product_id'])) {
                    $pid = $product['product_id'];
                    if (isset($merged[$pid])) {
                        $merged[$pid]['quantity'] += (int)($product['quantity'] ?? 0);
                    } else {
                        $merged[$pid] = $product;
                    }
                }
            }
            $this->products = array_values($merged);
        }
    }

    protected function getAccountModel()
    {
        if ($this->_accountModel === null && $this->account_id) {
            $this->_accountModel = Account::find()
                ->andWhere(['id' => $this->account_id, 'status' => 1])
                ->with('membershipLevel')
                ->one();
        }
        return $this->_accountModel;
    }

    protected function getProductModels()
    {
        if (empty($this->_productModels) && is_array($this->products)) {
            $productIds = array_column($this->products, 'product_id');
            $productIds = array_filter($productIds, 'is_numeric');
            if (!empty($productIds)) {
                $this->_productModels = Product::find()
                    ->andWhere(['id' => $productIds])
                    ->notDeleted()
                    ->indexBy('id')
                    ->all();
            }
        }
        return $this->_productModels;
    }

    public function validateProducts($attribute)
    {
        $products = $this->products;
        if (!is_array($products) || empty($products)) {
            $this->addError($attribute, 'Products must be a non-empty array.');
            return;
        }

        $hasStructuralError = false;
        foreach ($products as $index => $product) {
            if (!is_array($product)) {
                $this->addError($attribute, "Product at index {$index} must be an array.");
                $hasStructuralError = true;
                continue;
            }
            if (!isset($product['product_id'])) {
                $this->addError($attribute, "Product at index {$index} must have a product_id.");
                $hasStructuralError = true;
                continue;
            }
            if (!isset($product['quantity']) || (int)$product['quantity'] <= 0) {
                $this->addError($attribute, "Product with ID {$product['product_id']} has an invalid quantity.");
                $hasStructuralError = true;
                continue;
            }
        }

        if ($hasStructuralError) {
            return;
        }

        $productModels = $this->getProductModels();

        foreach ($products as $product) {
            $productId = $product['product_id'];
            if (!isset($productModels[$productId])) {
                $this->addError($attribute, "Product with ID {$productId} does not exist or has been deleted.");
            }
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $data = $this->calculate();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = new Order();
            // generateOrderCode() sets $order->order_code internally
            $order->generateOrderCode();
            $order->setAttributes([
                'account_id' => $this->account_id,
                'shipping_name' => $this->shipping_name,
                'shipping_email' => $this->shipping_email,
                'shipping_phone' => $this->shipping_phone,
                'shipping_address' => $this->shipping_address,
                'membership_level_id' => $data['membership_level_id'],
                'pay_method' => $this->pay_method,
                'status' => $this->status,
                'subtotal' => $data['subtotal'],
                'discount' => $data['discount'],
                'shipping_fee' => $data['shipping_fee'],
                'final_total' => $data['final_total'],
            ]);

            if (!$order->save()) {
                $this->addErrors($order->getErrors());
                throw new \yii\db\Exception('Failed to save order.');
            }

            if (!$this->saveOrderItems($order, $data['items'])) {
                throw new \yii\db\Exception('Failed to save order items.');
            }

            $transaction->commit();
            $this->id = $order->id;
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    protected function saveOrderItems(Order $order, array $calculatedItems): bool
    {
        foreach ($this->products as $productData) {
            $productId = $productData['product_id'];
            $calculatedItem = $calculatedItems[$productId] ?? null;
            if (!$calculatedItem) {
                $this->addError('products', "Product with ID {$productId} does not exist.");
                return false;
            }

            $product = $calculatedItem['product'];

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->quantity = (int)$productData['quantity'];
            $orderItem->unit_price = $calculatedItem['unit_price'];
            $orderItem->total_price = $calculatedItem['total_price'];
            $orderItem->product_name = $product->name;

            if (!$orderItem->save()) {
                $this->addErrors($orderItem->getErrors());
                return false;
            }
        }
        return true;
    }

    protected function calculate()
    {
        $calculatedItems = [];
        $subtotal = 0;
        $productModels = $this->getProductModels();

        foreach ($this->products as $productData) {
            if (is_array($productData) && isset($productData['product_id'])) {
                $productId = $productData['product_id'];
                $product = $productModels[$productId] ?? null;
                if ($product) {
                    $discount = $product->discount ?? 0;
                    $unitPrice = $discount > 0 ? $product->price * (1 - ($discount / 100)) : $product->price;
                    $totalPrice = $unitPrice * $productData['quantity'];

                    $subtotal += $totalPrice;
                    $calculatedItems[$productId] = [
                        'product' => $product,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }
            }
        }

        $membershipDiscountData = $this->calculateMembershipDiscount($subtotal);
        $shippingFee = 0;
        $finalTotal = $subtotal - $membershipDiscountData['discount'] + $shippingFee;

        return [
            'subtotal' => $subtotal,
            'discount' => $membershipDiscountData['discount'],
            'shipping_fee' => $shippingFee,
            'final_total' => $finalTotal,
            'membership_level_id' => $membershipDiscountData['membership_level_id'],
            'items' => $calculatedItems,
        ];
    }

    protected function calculateMembershipDiscount($subtotal)
    {
        $discount = 0;
        $membershipLevelId = null;
        $account = $this->getAccountModel();
        if ($account) {
            $membershipLevelId = $account->membership_level_id;
            $membershipLevel = $account->membershipLevel;
            if ($membershipLevel && $membershipLevel->discount_rate > 0) {
                $discount = round($subtotal * ($membershipLevel->discount_rate / 100), 2);
            }
        }

        return [
            'discount' => $discount,
            'membership_level_id' => $membershipLevelId,
        ];
    }
}
