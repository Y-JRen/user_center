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
                    'is_show' => $post['is_show'][$key],
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
}