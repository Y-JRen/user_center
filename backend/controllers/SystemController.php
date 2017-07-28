<?php
/**
 * Created by PhpStorm.
 * User: huawei
 * Date: 2017/6/30
 * Time: 17:31
 */

namespace backend\controllers;


use common\models\SystemConf;
use Yii;
use yii\helpers\ArrayHelper;

class SystemController extends BaseController
{
    public function actionRate()
    {
        $model = SystemConf::find()->where(['key' => 'rate'])->one();
        $data = json_decode(ArrayHelper::getValue($model, 'value', ''), true);

        $defaultChecked = ArrayHelper::getValue($data, 'default_checked', []);
        $isModify = ArrayHelper::getValue($data, 'is_modify', []);

        if (Yii::$app->request->isPost) {
            $info = [];
            $post = Yii::$app->request->post();

            foreach ($post['type'] as $key => $value) {
                if (empty($post['type'][$key]) || empty($post['label'][$key]) || empty($post['ratio'][$key]) || empty($post['is_show'][$key])) {
                    continue;
                }
                $capped = empty($post['capped'][$key]) ? 0 : $post['capped'][$key];
                $info[$key] = [
                    'type' => $post['type'][$key],
                    'label' => $post['label'][$key],
                    'ratio' => $post['ratio'][$key],
                    'capped' => $capped,
                    'is_show' => current($post['is_show'][$key]),
                    'is_modify_rate' => current($post['is_modify_rate'][$key]),
                ];
            }

            $defaultChecked = ArrayHelper::getValue($post, 'default_checked', ArrayHelper::getValue(current($info), 'type'));

            $isModify = ArrayHelper::getValue($post, 'is_modify', 1);

            $model->value = json_encode(
                [
                    'info' => $info,
                    'default_checked' => $defaultChecked,
                    'is_modify' => $isModify
                ]);

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            } else {
                return $this->redirect(['rate']);
            }
        }

        $info = ArrayHelper::getValue($data, 'info', []);

        return $this->render('rate', ['info' => $info, 'defaultChecked' => $defaultChecked, 'isModify' => $isModify]);
    }

    /**
     * 设置基本参数
     * @return string
     */
    public function actionBase()
    {
        $configArr = SystemConf::find()->where(['is_show' => 1])->all();
        $post = Yii::$app->request->post();
        if ($post) {
            $errorArr = [];
            foreach ($configArr as $config) {
                /* @var $config SystemConf */
                $config->value = $post['value'][$config->key];
                if (!$config->save()) {
                    $errorArr[] = $config->label . '提交失败；失败原因：' . $config->getFirstError('value');
                }
            }
            if ($errorArr) {
                Yii::$app->session->setFlash('error', join("<br>", $errorArr));
            } else {
                Yii::$app->session->setFlash('success', '提交成功');
                return $this->redirect(['base']);
            }
        }
        return $this->render('base', [
            'data' => $configArr
        ]);
    }
}