<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coupon_usage".
 *
 * @property int $id
 * @property int $coupon_id
 * @property string $order_id
 * @property int $account_id
 * @property string $applied_code
 * @property float $applied_value
 * @property string $applied_type
 * @property float $applied_max_amount
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $account
 * @property Coupon $coupon
 * @property Order $order
 */
class CouponUsage extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coupon_usage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['coupon_id', 'order_id', 'account_id', 'applied_code', 'applied_value', 'applied_type', 'applied_max_amount'], 'required'],
            [['coupon_id', 'account_id'], 'integer'],
            [['applied_value', 'applied_max_amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['order_id', 'applied_code', 'applied_type'], 'string', 'max' => 255],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['coupon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['coupon_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coupon_id' => 'Coupon ID',
            'order_id' => 'Order ID',
            'account_id' => 'Account ID',
            'applied_code' => 'Applied Code',
            'applied_value' => 'Applied Value',
            'applied_type' => 'Applied Type',
            'applied_max_amount' => 'Applied Max Amount',
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
     * Gets query for [[Coupon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCoupon()
    {
        return $this->hasOne(Coupon::class, ['id' => 'coupon_id']);
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

}
