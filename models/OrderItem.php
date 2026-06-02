<?php

namespace app\models;

use app\models\base\BaseOrderItem;
use yii\behaviors\TimestampBehavior;

class OrderItem extends BaseOrderItem
{

    public function behaviors()
    {
        return[
            [
                'class' => TimestampBehavior::class,
                'value' => function (){
                   return date('Y-m-d H:i:s');
                }
            ]
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

}
