<?php

namespace app\controllers;

use app\models\Account;

class AccountController extends ApiController
{
    protected function optionAuthActions()
    {
        return ['index'];
    }

    public function actionIndex()
    {
        $accounts = Account::find()->where(['status' => 1])->all();
        if (empty($accounts)) {
            $accounts = Account::find()->all();
        }

        $data = [];
        foreach ($accounts as $acc) {
            $data[] = [
                'id' => $acc->id,
                'name' => $acc->name,
                'email' => $acc->email,
                'status' => $acc->status,
                'membership_level_name' => $acc->membershipLevel->name ?? 'Đồng',
                'membership_level_discount' => (float)($acc->membershipLevel->discount_rate ?? 0),
            ];
        }

        return $this->success($data);
    }
}
