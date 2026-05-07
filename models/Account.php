<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property int $role_id
 * @property int $membership_level_id
 * @property float|null $total_point
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Cart[] $carts
 * @property Comment[] $comments
 * @property CouponUsage[] $couponUsages
 * @property MembershipLevel $membershipLevel
 * @property Order[] $orders
 * @property Post[] $posts
 * @property Profile[] $profiles
 * @property Rating[] $ratings
 * @property Role $role
 */
class Account extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['total_point'], 'default', 'value' => 0.00],
            [['status'], 'default', 'value' => 1],
            [['role_id', 'membership_level_id', 'name', 'email', 'password'], 'required'],
            [['role_id', 'membership_level_id', 'status'], 'integer'],
            [['total_point'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'password'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['membership_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => MembershipLevel::class, 'targetAttribute' => ['membership_level_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'membership_level_id' => 'Membership Level ID',
            'total_point' => 'Total Point',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Carts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(Cart::class, ['account_id' => 'id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['account_id' => 'id']);
    }

    /**
     * Gets query for [[CouponUsages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCouponUsages()
    {
        return $this->hasMany(CouponUsage::class, ['account_id' => 'id']);
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

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['account_id' => 'id']);
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['id' => 'post_id'])->viaTable('rating', ['account_id' => 'id']);
    }

    /**
     * Gets query for [[Profiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::class, ['account_id' => 'id']);
    }

    /**
     * Gets query for [[Ratings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatings()
    {
        return $this->hasMany(Rating::class, ['account_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

}
