<?php

namespace app\models;

use Override;
use yii\behaviors\TimestampBehavior;

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

    #[Override]
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ]
        ];
    }

    #[Override]
    public function beforeSave($insert)
    {
        if ($insert) {
            if (empty($this->order_code)) {
                $date = date('Ymd');
                $prefix = 'ORD' . $date;

                $lastOrder = self::find()
                    ->where(['like', 'order_code', $prefix . '%', false])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                if ($lastOrder) {
                    $lastNum = (int) substr($lastOrder->order_code, -4);
                    $newOrd = $lastNum + 1;
                } else {
                    $newOrd = 1;
                }
                $this->order_code = $prefix . str_pad($newOrd, 4, '0', STR_PAD_LEFT);
            }
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['membership_level_id', 'subtotal', 'final_total', 'created_at', 'updated_at', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address'], 'default', 'value' => null],
            [['discount', 'shipping_fee'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],
            [['account_id', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address','pay_method'], 'required'],
            [['account_id', 'membership_level_id', 'pay_method', 'status'], 'integer'],
            [['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address'], 'string'],
            [['discount', 'subtotal', 'final_total', 'shipping_fee'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['order_code', 'shipping_name', 'shipping_email'], 'string', 'max' => 255],
            [['order_code'], 'unique'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_code' => 'Order Code',
            'account_id' => 'Account ID',
            'membership_level_id' => 'Membership Level ID',
            'shipping_name' => 'Name',
            'shipping_email' => 'Email',
            'shipping_phone' => 'Phone',
            'shipping_address' => 'Address',
            'discount' => 'Discount Amount',
            'subtotal' => 'Subtotal',
            'final_total' => 'Final Total',
            'pay_method' => 'Payment Method',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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

    public static function find()
    {
        return new query\OrderQuery(get_called_class());
    }
}
