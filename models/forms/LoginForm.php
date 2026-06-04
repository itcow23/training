<?php

namespace app\models\forms;

use yii\base\Model;
use app\models\Account;
use Yii;

class LoginForm extends Model
{
    private $account;
    public $email;
    public $password;
    public $rememberMe = true;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['rememberMe'], 'boolean'],
            [['email'], 'email'],
            [['password'], 'validatePassword']
        ];
    }

    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('Login', 'Incorrect username or password.');
            }
            return true;
        }
    }

    public function getUser()
    {
        if ($this->account === null) {
            $this->account = Account::find()
                ->where(['email' => $this->email])
                ->one();
        }
        return $this->account;
    }

    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user) {
                Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
                return true;
            }
        }
        return false;
    }
}
