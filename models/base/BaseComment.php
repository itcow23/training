<?php

namespace app\models\base;

use app\models\Account;
use app\models\Post;
use Yii;

/**
 * This is the base model class for table "comment".
 *
 * @property int $id
 * @property int $account_id
 * @property int $post_id
 * @property string $content
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 * @property Account $account
 * @property Post $post
 */
class BaseComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'post_id', 'content'], 'required'],
            [['account_id', 'post_id'], 'integer'],
            [['content'], 'string'],
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
            'account_id' => 'Account ID',
            'post_id' => 'Post ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'deleted_at' => 'Deleted At',
        ];
    }
}
