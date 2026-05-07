<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coupon".
 *
 * @property int $id
 * @property string $code
 * @property string $type
 * @property float $value
 * @property float $max_amount
 * @property float $min_purchase
 * @property int $usage_limit
 * @property int|null $status
 * @property string $start_date
 * @property string $expiry_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property CouponUsage[] $couponUsages
 */
class Coupon extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['code', 'type', 'value', 'max_amount', 'min_purchase', 'usage_limit', 'start_date', 'expiry_date'], 'required'],
            [['value', 'max_amount', 'min_purchase'], 'number'],
            [['usage_limit', 'status'], 'integer'],
            [['start_date', 'expiry_date', 'created_at', 'updated_at'], 'safe'],
            [['code', 'type'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'type' => 'Type',
            'value' => 'Value',
            'max_amount' => 'Max Amount',
            'min_purchase' => 'Min Purchase',
            'usage_limit' => 'Usage Limit',
            'status' => 'Status',
            'start_date' => 'Start Date',
            'expiry_date' => 'Expiry Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[CouponUsages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCouponUsages()
    {
        return $this->hasMany(CouponUsage::class, ['coupon_id' => 'id']);
    }

}
