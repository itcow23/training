<?php

namespace app\models;

use app\models\base\BaseRating;
use yii\behaviors\TimestampBehavior;
use app\models\Post;

class Rating extends BaseRating
{
    public function behaviors()
    {
        return [
            'timestamps' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ],
            'softDelete' => [
                'class' => \app\behaviors\SoftDeleteBehavior::class,
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->updatePostAvgRating();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->updatePostAvgRating();
    }

    protected function updatePostAvgRating()
    {
        $avg = self::find()->andWhere(['post_id' => $this->post_id])->average('score');
        Post::updateAll(['avg_rating' => round($avg ?: 0, 1)], ['id' => $this->post_id]);
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
        return parent::find()->andWhere(['rating.is_deleted' => 0]);
    }

    public static function findWithDeleted()
    {
        return parent::find();
    }
}
