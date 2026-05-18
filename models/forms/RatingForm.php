<?php

namespace app\models\forms;

use yii\base\Model;

class RatingForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $post_id;
    public $account_id;
    public $score;

    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['post_id', 'account_id', 'score'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['post_id', 'account_id', 'score'], 'required'],
            [['post_id', 'account_id', 'score'], 'integer'],
            ['score', 'integer', 'min' => 1, 'max' => 5],
        ];
    }

    public function attributeLabels()
    {
        return [
            'post_id' => 'Post',
            'account_id' => 'Account',
            'score' => 'Score',
        ];
    }
}
