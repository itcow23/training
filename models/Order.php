<?php

namespace app\models;

use Override;
use yii\behaviors\TimestampBehavior;

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
            if (empty($this->id)) {

                $date = date('Ymd');
                $prefix = 'ORD' . $date;

                $lastOrder = self::find()
                    ->where(['like', 'id', $prefix . '%', false])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                if ($lastOrder) {
                    $lastNum = (int) substr($lastOrder->id, -2);
                    $newOrd = $lastNum + 1;
                } else {
                    $newOrd = 1;
                }
                $this->id = $prefix . str_pad($newOrd, 4, '0', STR_PAD_LEFT);
            }
        }
        return parent::beforeSave($insert);
    }


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
            [['account_id', 'name', 'email', 'phone', 'address','pay'], 'required'],
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

    public static function find()
    {
        return new query\OrderQuery(get_called_class());
    }
}
