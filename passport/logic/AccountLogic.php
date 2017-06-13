<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/12
 * Time: 17:41
 */

namespace passport\logic;


use Yii;
use common\models\UserAccount;

class AccountLogic extends Logic
{
    /**
     * @param $data
     * @return boolean
     */
    public function addAccount($data)
    {

        $account = UserAccount::findOne([
            'uid' => Yii::$app->user->id,
            'account' => $data['account'],
            'type' => 3
        ]);
        if (empty($account)) {
            $account = new UserAccount();
            $account->uid = Yii::$app->user->id;
            $account->setAttributes($data);
            $account->updated_at = time();
            $account->type = 3;
            if ($account->save()) {
                return true;
            } else {
                Yii::error(print_r(['errors' => $account->errors, 'attributes' => $account->attributes], true));
                return false;
            }
        }
        return true;
    }

    /**
     * 账号列表
     *
     * @param integer $userId
     * @return array
     */
    public function accountList($userId)
    {
        return UserAccount::find()->where(['uid' => $userId])->orderBy(['id' => 'DESC'])->all();
    }
}