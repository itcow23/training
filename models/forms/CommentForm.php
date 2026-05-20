<?php

namespace app\models\forms;

use app\models\forms\BaseForm;

class CommentForm extends BaseForm
{
    public $account_id;
    public $post_id;
    public $content;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['account_id', 'post_id', 'content'];
        $scenarios[self::SCENARIO_UPDATE] = ['account_id', 'post_id', 'content'];
        $scenarios[self::SCENARIO_DELETE] = ['account_id'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['account_id', 'post_id', 'content'], 'required'],
            [['account_id', 'post_id'], 'integer'],
            [['content'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id' => 'Account',
            'post_id' => 'Post',
            'content' => 'Content',
        ];
    }
}
