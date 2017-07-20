<?php
namespace passport\modules\inside\controllers;

use common\models\LakalaStatic;
use common\models\SystemConf;
use yii;

class LakalaController extends BaseController
{
    /**
     * 获取拉卡拉版本号
     * @return array
     */
    public function actionVersion()
    {
        /* @var $model SystemConf */
        $model = SystemConf::find()->where(['key' => 'lakala_version'])->one();
        return $this->_return(['version' => $model->value]);
    }

    /**
     * 获取拉卡拉POS应用的更新统计
     * @return array
     */
    public function actionUpdateStatic()
    {
        $model = new LakalaStatic();
        $model->created_at = time();
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return $this->_return('ok');
        } else {
            return $this->_return('error');
        }
    }
}