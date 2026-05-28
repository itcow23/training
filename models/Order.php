<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\models\query\OrderQuery;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property string $order_code
 * @property int $account_id
 * @property int|null $membership_level_id
 * @property float|null $subtotal
 * @property float|null $discount
 * @property float|null $shipping_fee
 * @property float|null $final_total
 * @property int|null $pay_method
 * @property int|null $status
 * @property string|null $shipping_name
 * @property string|null $shipping_email
 * @property string|null $shipping_phone
 * @property string|null $shipping_address
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $account
 * @property CouponUsage[] $couponUsages
 * @property OrderItem[] $orderItems
 */
class Order extends \yii\db\ActiveRecord
{
    public const STATUS_PENDING = 1;
    public const STATUS_CONFIRM = 2;
    public const STATUS_SHIPPING = 3;
    public const STATUS_COMPLETED = 4;
    public const STATUS_CANCEL = 0;

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
                        'product_name' => $orderItem->product->name ?? null,
                        'discount' => $orderItem->product->discount . '%' ?? '0%',
                        'unit_price' => (float)$orderItem->unit_price,
                        'quantity' => (int)$orderItem->quantity,
                        'total_price' => (float)$orderItem->total_price
                    ];
                }, $model->orderItems);
            }
        ];
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

    public function beforeSave($insert)
    {
        if ($insert && empty($this->order_code)) {
            $this->generateOrderCode();
        }
        return parent::beforeSave($insert);
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
        return (new OrderQuery(get_called_class()))->andWhere(['orders.is_deleted' => 0]);
    }

    public static function findWithDeleted()
    {
        return new OrderQuery(get_called_class());
    }
}
