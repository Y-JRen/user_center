<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/12
 * Time: 17:41
 */

namespace passport\logic;


use common\models\User;
use common\models\UserAccount;

class AccountLogic extends Logic
{
    /**
     * @param User $user
     * @param $data
     * @return boolean
     */
    public function addAccount($user, $data)
    {
        $account = UserAccount::findOne([
            'uid' => $user->id,
            'account' => $data['account'],
            'type' => 3
        ]);
        if(empty($account)) {
            $account = new UserAccount();
            $account->uid = $user->id;
            $account->bank_name = $data['bank_name'];
            $account->account = $data['account'];
            $account->branch_name = $data['branch_name'];
            $account->updated_at = time();
            return $account->save();
        }
        return true;
    }
}