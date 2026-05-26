<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\models\Post;

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
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['account_id', 'post_id', 'content'];
        $scenarios[self::SCENARIO_UPDATE] = ['content'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['post_id'], 'exist', 'targetClass' => Post::class, 'targetAttribute' => 'id'],
            [['account_id', 'post_id', 'content'], 'required'],
            [['account_id', 'post_id'], 'integer'],
            [['content'], 'string'],
        ];
    }

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
    public static function find()
    {
        return parent::find()->andWhere(['comment.is_deleted' => 0]);
    }

    public static function findWithDeleted()
    {
        return parent::find();
    }
}
