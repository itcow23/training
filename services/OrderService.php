<?php

namespace app\services;

use Throwable;
use RuntimeException;
use Yii;
use yii\web\HttpException;
use app\models\Product;


class OrderService
{
    private OrderDetailService $orderDetailService;
    public function __construct()
    {
        $this->orderDetailService = new OrderDetailService();
    }

    public function create($model, $postData)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            if (!$model->load($postData, '')) {
                throw new RuntimeException('Failed to load.');
            }

            $result = $this->calculateSubtotal($postData['products']);
            $subtotal = $result['subtotal'];
            $productList = $result['productList'];

            $discountAmount = $this->calculateDiscount($subtotal, $model->membership_level_id);

            $model->subtotal = $subtotal;
            $model->discount_amount = $discountAmount;
            $model->final_total = $subtotal - $discountAmount;

            if (!$model->save()) {

                throw new HttpException(
                    422,
                    json_encode($model->errors)
                );
            }

            $this->orderDetailService->create($model->id, $productList, $postData['products']);

            $transaction->commit();

            return true;
        } catch (Throwable $e) {

            $transaction->rollBack();

            $model->addError('order', $e->getMessage());

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

            $product = $productList[$item['product_id']];

            if (!$product) {
                throw new HttpException(404, 'Product not found');
            }

            $subtotal += $product->price * $item['quantity'];
        }
        return [
            'subtotal' => $subtotal,
            'productList' => $productList
        ];
    }

    private function calculateDiscount($subtotal, $membershipLevelId)
    {
        return match ($membershipLevelId) {
            '1' => $subtotal * 0.1,
            '2' => $subtotal * 0.2,
            '3' => $subtotal * 0.3,
            default => 0
        };
    }

    public function updateStatusOrder($model, $postData)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            if (!$model->load($postData, '')) {
                throw new RuntimeException('Load data failed.');
            }

            $validateStatus = $this->validateStatusTransition($model, $model->getOldAttribute('status'), $model->status);

            if (!$validateStatus) {
                throw new RuntimeException('Invalid status transition.');
            }

            if (!$model->save()) {
                throw new RuntimeException(json_encode($model->errors));
            }

            $transaction->commit();

            return true;
        } catch (Throwable $e) {

            $transaction->rollBack();

            $model->addError('status', $e->getMessage());

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
