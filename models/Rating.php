<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use app\models\Post;

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
    const SCENARIO_CREATE = 'create';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['post_id', 'account_id', 'score'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['post_id'], 'exist', 'targetClass' => Post::class, 'targetAttribute' => 'id'],
            [['post_id', 'account_id', 'score'], 'required'],
            [['post_id', 'account_id'], 'integer'],
            ['score', 'integer', 'min' => 1, 'max' => 5],
            [['post_id', 'account_id'], 'unique', 'targetAttribute' => ['post_id', 'account_id'], 'message' => 'You have already rated this post.'],
        ];
    }

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

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
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
        $avg = self::find()->where(['post_id' => $this->post_id])->average('score');
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
