<?php

namespace app\models;

use app\models\base\BaseOrder;
use yii\behaviors\TimestampBehavior;
use app\models\query\OrderQuery;
use Yii;

class Order extends BaseOrder
{

    public const STATUS_PENDING = 1;
    public const STATUS_CONFIRM = 2;
    public const STATUS_SHIPPING = 3;
    public const STATUS_COMPLETED = 4;
    public const STATUS_CANCEL = 0;

    public const PAY_METHOD_COD = 1;
    public const PAY_METHOD_BANK_TRANSFER = 2;
    public const PAY_METHOD_CREDIT_CARD = 3;
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'softDelete' => [
                'class' => \app\behaviors\SoftDeleteBehavior::class,
            ],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'order_code',
            'account_id',
            'shipping_name',
            'shipping_email',
            'shipping_phone',
            'shipping_address',
            'membership_level_id',
            'membership_level' => function ($model) {
                return $model->membershipLevel ? $model->membershipLevel->name : null;
            },
            'discount',
            'subtotal',
            'shipping_fee',
            'final_total',
            'pay_method',
            'status',
            'created_at',
            'items' => function ($model) {
                return array_map(function ($orderItem) {
                    return [
                        'product_id' => $orderItem->product_id,
                        'product_name' => $orderItem->product_name ?? ($orderItem->product->name ?? ('Sản phẩm ID: ' . $orderItem->product_id)),
                        'discount' => isset($orderItem->product) ? ($orderItem->product->discount . '%') : '0%',
                        'unit_price' => (float)$orderItem->unit_price,
                        'quantity' => (int)$orderItem->quantity,
                        'total_price' => (float)$orderItem->total_price
                    ];
                }, $model->orderItems);
            }
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

    /**
     * Gets query for [[CouponUsages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCouponUsages()
    {
        return $this->hasMany(CouponUsage::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[MembershipLevel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMembershipLevel()
    {
        return $this->hasOne(MembershipLevel::class, ['id' => 'membership_level_id']);
    }

    public static function find()
    {
        return (new OrderQuery(get_called_class()));
    }

    public function generateOrderCode(int $attempts = 0): void
    {
        if ($attempts >= 10) {
            throw new \RuntimeException('Cannot generate a unique order code after 10 attempts.');
        }

        $date = date('YmdHi');
        $random = strtoupper(Yii::$app->security->generateRandomString(4));
        $this->order_code = "ORD{$date}{$random}";

        if (self::find()->andWhere(['order_code' => $this->order_code])->exists()) {
            $this->generateOrderCode($attempts + 1);
        }
    }

    public function validateStatusTransition(int $newStatus): bool
    {
        $allowedTransitions = [
            self::STATUS_PENDING => [
                self::STATUS_CONFIRM,
                self::STATUS_CANCEL
            ],
            self::STATUS_CONFIRM => [
                self::STATUS_SHIPPING,
                self::STATUS_CANCEL
            ],
            self::STATUS_SHIPPING => [
                self::STATUS_COMPLETED
            ],
            self::STATUS_COMPLETED => [],
            self::STATUS_CANCEL => [],
        ];

        return in_array($newStatus, $allowedTransitions[(int)$this->status] ?? []);
    }
}
