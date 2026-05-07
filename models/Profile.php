<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $account_id
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $img
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $account
 */
class Profile extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'address', 'img', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['account_id'], 'required'],
            [['account_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['phone', 'address', 'img'], 'string', 'max' => 255],
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
            'phone' => 'Phone',
            'address' => 'Address',
            'img' => 'Img',
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

}
