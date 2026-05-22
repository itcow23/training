<?php

namespace app\models;

use Override;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $account_id
 * @property int $post_id
 * @property string $content
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $account
 * @property Post $post
 */
class Comment extends \yii\db\ActiveRecord
{

    #[Override]
    public function behaviors()
    {
        return[
            'timestamps' => [
                'class' => TimestampBehavior::class,
                'value' => function(){
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
        return 'comment';
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
