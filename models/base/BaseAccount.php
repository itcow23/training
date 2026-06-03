<?php

namespace app\models\base;

use app\models\MembershipLevel;

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
 */
class BaseAccount extends \yii\db\ActiveRecord
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

}
