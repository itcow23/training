<?php

namespace app\models\query;

use app\models\Order;

/**
 * This is the ActiveQuery class for [[\app\models\Order]].
 *
 * @see \app\models\Order
 */
class OrderQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function pending()
    {
        return $this->andWhere(['status' => 1]);
    }

    public function confirm()
    {
        return $this->andWhere(['status' => 2]);
    }

    public function shipping()
    {
        return $this->andWhere(['status' => 3]);
    }

    public function completed()
    {
        return $this->andWhere(['status' => 4]);
    }

    public function cancel()
    {
        return $this->andWhere(['status' => 0]);
    }

    public function filterByStatus($status)
    {
        return match ($status) {
            Order::STATUS_PENDING => $this->pending(),
            Order::STATUS_CONFIRM => $this->confirm(),
            Order::STATUS_SHIPPING => $this->shipping(),
            Order::STATUS_COMPLETED => $this->completed(),
            Order::STATUS_CANCEL => $this->cancel(),
            default => $this
        };
    }
}
