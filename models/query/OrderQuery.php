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

    public function withRelations()
    {
        return $this->with([
            'orderItems.product',
            'membershipLevel',
        ]);
    }

    public function keyword(?string $keyword)
    {
        return $this->andFilterWhere([
            'like',
            'order_code',
            $keyword,
        ]);
    }

    public function withDeleted()
    {
        if (is_array($this->where)) {
            $this->where = $this->removeIsDeletedCondition($this->where);
        }
        return $this;
    }

    private function removeIsDeletedCondition($where)
    {
        if (!is_array($where)) {
            return $where;
        }

        if (isset($where['orders.is_deleted'])) {
            unset($where['orders.is_deleted']);
        }
        if (isset($where['is_deleted'])) {
            unset($where['is_deleted']);
        }

        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $where[$key] = $this->removeIsDeletedCondition($value);
                if (is_array($where[$key]) && count($where[$key]) === 0) {
                    unset($where[$key]);
                }
            }
        }

        if (count($where) === 1 && in_array(strtolower(reset($where)), ['and', 'or'])) {
            return [];
        }

        return $where;
    }
}


