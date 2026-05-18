<?php

namespace app\services;

use Throwable;
use RuntimeException;
use app\models\forms\OrderForm;
use app\models\response\OrderResponse;
use Yii;
use app\models\Product;

class OrderService
{
    private OrderDetailService $orderDetailService;
    public function __construct()
    {
        $this->orderDetailService = new OrderDetailService();
    }

    public function create(OrderResponse $model, OrderForm $form)
    {
        if (!$form->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model->setAttributes($form->getAttributes(), false);

            if (empty($form->products)) {
                throw new RuntimeException('Products list is required.');
            }

            $result = $this->calculateSubtotal($form->products);
            $subtotal = $result['subtotal'];
            $productList = $result['productList'];

            $discountAmount = $this->calculateDiscount($subtotal, $model->membership_level_id);

            $model->subtotal = $subtotal;
            $model->discount_amount = $discountAmount;
            $model->final_total = $subtotal - $discountAmount;

            if (!$model->save()) {
                throw new RuntimeException(json_encode($model->errors));
            }

            $this->orderDetailService->create($model->id, $productList, $form->products);

            $transaction->commit();

            return OrderResponse::find()
                ->where(['id' => $model->id])
                ->with(['orderDetails.product'])
                ->one();
        } catch (Throwable $e) {
            $transaction->rollBack();
            $form->addError('order', $e->getMessage());

            return false;
        }
    }

    private function calculateSubtotal($products)
    {
        $subtotal = 0;

        $productIds = array_column($products,'product_id');

        $productList = Product::find()
                        ->where(['id' => $productIds])
                        ->indexBy('id')
                        ->all();
        foreach ($products as $item) {
            if (!isset($productList[$item['product_id']])) {
                throw new RuntimeException('Product does not exist.');
            }

            $product = $productList[$item['product_id']];

            $subtotal += $product->price * $item['quantity'];
        }
        return [
            'subtotal' => $subtotal,
            'productList' => $productList
        ];
    }

    private function calculateDiscount($subtotal, $membershipLevelId)
    {
        
        return match ((int)$membershipLevelId) {
            1 => $subtotal * 0.1,
            2 => $subtotal * 0.2,
            3 => $subtotal * 0.3,
            default => 0
        };
    }

    public function delete(OrderResponse $model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->delete()) {
                throw new RuntimeException('Failed to delete order.');
            }
            $transaction->commit();
            return true;
        } catch (Throwable $e) {
            $transaction->rollBack();
            return false;
        }
    }
    public function updateStatusOrder(OrderResponse $model, OrderForm $form)
    {
        if (!$form->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $newStatus = (int)$form->status;
            $oldStatus = (int)$model->status;

            $validateStatus = $this->validateStatusTransition($model, $oldStatus, $newStatus);

            if (!$validateStatus) {
                throw new RuntimeException('Invalid status transition.');
            }

            $model->status = $newStatus;

            if (!$model->save()) {
                throw new RuntimeException(json_encode($model->errors));
            }

            $transaction->commit();

            return OrderResponse::find()
                ->where(['id' => $model->id])
                ->with(['orderDetails.product'])
                ->one();
        } catch (Throwable $e) {
            $transaction->rollBack();
            $form->addError('status', $e->getMessage());

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
