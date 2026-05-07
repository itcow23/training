<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "membership_level".
 *
 * @property int $id
 * @property string $name
 * @property float $point_required
 * @property float $discount_rate
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account[] $accounts
 */
class MembershipLevel extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'membership_level';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['name', 'point_required', 'discount_rate'], 'required'],
            [['point_required', 'discount_rate'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'point_required' => 'Point Required',
            'discount_rate' => 'Discount Rate',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Accounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['membership_level_id' => 'id']);
    }

}
