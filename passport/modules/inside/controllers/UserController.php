<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/15
 * Time: 18:04
 */

namespace passport\modules\inside\controllers;


use Yii;
use passport\modules\inside\models\User;
use passport\helpers\Config;
use yii\helpers\ArrayHelper;

class UserController extends BaseController
{
    public function verbs()
    {
        return [
            'reg' => ['POST'],
            'check' => ['GET'],
        ];
    }

    /**
     * 用户注册
     */
    public function actionReg()
    {
        $phone = Yii::$app->request->post('phone');
        $model = User::find()->where(['phone' => $phone])->one();

        if ($model) {
            return $this->_return(['uid' => $model->id]);
        } else {
            /* @var $model User */
            $model = new User();
            $model->phone = $phone;
            $model->user_name = (empty($model->user_name) ? $phone : $model->user_name);
            $model->from_platform = Config::getPlatform();
            $model->from_channel = Yii::$app->request->post('channel', '');
            $model->reg_time = time();
            $model->reg_ip = Yii::$app->request->getUserIp();
            $model->login_time = 0;
            $model->status = 1;
            if ($model->save()) {
                return $this->_return(['uid' => $model->id]);
            } else {
                return $this->_error(1002, current($model->getFirstErrors()));
            }
        }
    }

    /**
     * 检测改uid的用户是否存在
     * @param $uid
     * @return array
     */
    public function actionCheck($uid)
    {
        $result = User::find()->select('phone')->where(['id' => $uid])->asArray()->one();
        $data = ['status' => !empty($result), 'phone' => ArrayHelper::getValue($result, 'phone')];
        return $this->_return($data);
    }
}