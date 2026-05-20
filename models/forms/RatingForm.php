<?php

namespace app\models\forms;

use app\models\forms\BaseForm;

class RatingForm extends BaseForm
{

    public $post_id;
    public $account_id;
    public $score;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
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
