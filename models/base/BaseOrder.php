<?php

namespace app\models\base;

use app\models\Account;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property string $order_code
 * @property int $account_id
 * @property int|null $membership_level_id
 * @property float $subtotal
 * @property float $discount
 * @property float $shipping_fee
 * @property float $final_total
 * @property string $currency
 * @property int $pay_method
 * @property int $status
 * @property string $shipping_name
 * @property string $shipping_email
 * @property string $shipping_phone
 * @property string $shipping_address
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 */
class BaseOrder extends \yii\db\ActiveRecord
{


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
            [['membership_level_id', 'created_at', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['shipping_fee'], 'default', 'value' => 0.00],
            [['currency'], 'default', 'value' => 'USD'],
            [['status'], 'default', 'value' => 1],
            [['is_deleted'], 'default', 'value' => 0],
            [['order_code', 'account_id', 'subtotal', 'final_total', 'pay_method', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address'], 'required'],
            [['account_id', 'membership_level_id', 'pay_method', 'status', 'is_deleted'], 'integer'],
            [['subtotal', 'discount', 'shipping_fee', 'final_total'], 'number'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['order_code'], 'string', 'max' => 20],
            [['currency'], 'string', 'max' => 3],
            [['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address'], 'string', 'max' => 255],
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
            'subtotal' => 'Subtotal',
            'discount' => 'Discount',
            'shipping_fee' => 'Shipping Fee',
            'final_total' => 'Final Total',
            'currency' => 'Currency',
            'pay_method' => 'Pay Method',
            'status' => 'Status',
            'shipping_name' => 'Shipping Name',
            'shipping_email' => 'Shipping Email',
            'shipping_phone' => 'Shipping Phone',
            'shipping_address' => 'Shipping Address',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'deleted_at' => 'Deleted At',
        ];
    }
}
