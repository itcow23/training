<?php

namespace app\models;

use app\models\base\BaseComment;
use yii\behaviors\TimestampBehavior;

class Comment extends BaseComment
{
    public function behaviors()
    {
        return [
            'timestamps' => [
                'class' => TimestampBehavior::class,
                'value' => function(){
                    return date('Y-m-d H:i:s');
                }
            ],
            'softDelete' => [
                'class' => \app\behaviors\SoftDeleteBehavior::class,
            ],
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

    public static function find()
    {
        return parent::find()->andWhere(['comment.is_deleted' => 0]);
    }

    public static function findWithDeleted()
    {
        return parent::find();
    }
}
