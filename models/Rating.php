<?php

namespace app\models;

use Override;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "rating".
 *
 * @property int $id
 * @property int $post_id
 * @property int $account_id
 * @property int $score
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $account
 * @property Post $post
 */
class Rating extends \yii\db\ActiveRecord
{

    #[Override]
    public function behaviors()
    {
        return [
            'timestamps' => [
                'class' => TimestampBehavior::class,
                'value'=> function (){
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['post_id', 'account_id', 'score'], 'required'],
            [['post_id', 'account_id'], 'integer'],
            ['score', 'integer', 'min' => 1, 'max' => 5],
            [['created_at', 'updated_at'], 'safe'],
            [['account_id', 'post_id'], 'unique', 'targetAttribute' => ['account_id', 'post_id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['account_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::class, 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'account_id' => 'Account ID',
            'score' => 'Score',
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
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

}
