<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property string $id
 * @property int $account_id
 * @property int|null $membership_level_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property float|null $discount_amount
 * @property float|null $subtotal
 * @property float|null $final_total
 * @property int|null $pay
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $account
 * @property CouponUsage[] $couponUsages
 * @property OrderDetail[] $orderDetails
 */
class Order extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['membership_level_id', 'subtotal', 'final_total', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['discount_amount'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],
            [['id', 'account_id', 'name', 'email', 'phone', 'address'], 'required'],
            [['account_id', 'membership_level_id', 'pay', 'status'], 'integer'],
            [['phone', 'address'], 'string'],
            [['discount_amount', 'subtotal', 'final_total'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'name', 'email'], 'string', 'max' => 255],
            [['id'], 'unique'],
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
            'account_id' => 'Account ID',
            'membership_level_id' => 'Membership Level ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'discount_amount' => 'Discount Amount',
            'subtotal' => 'Subtotal',
            'final_total' => 'Final Total',
            'pay' => 'Pay',
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
     * Gets query for [[OrderDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderDetails()
    {
        return $this->hasMany(OrderDetail::class, ['order_id' => 'id']);
    }

}
