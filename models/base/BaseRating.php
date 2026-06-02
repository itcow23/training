<?php

namespace app\models\base;

use app\models\Account;
use app\models\Post;
use Yii;

/**
 * This is the base model class for table "rating".
 *
 * @property int $id
 * @property int $post_id
 * @property int $account_id
 * @property int $score
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 * @property Account $account
 * @property Post $post
 */
class BaseRating extends \yii\db\ActiveRecord
{
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
            [['post_id', 'account_id', 'score'], 'required'],
            [['post_id', 'account_id', 'score'], 'integer'],
            [['score'], 'integer', 'min' => 1, 'max' => 5],
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
            'is_deleted' => 'Is Deleted',
            'deleted_at' => 'Deleted At',
        ];
    }
}
