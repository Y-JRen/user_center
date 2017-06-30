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

        if (Yii::$app->request->isPost) {
            $info = [];
            $post = Yii::$app->request->post();
            foreach ($post['type'] as $key => $value) {
                $info[$key] = [
                    'type' => $post['type'][$key],
                    'label' => $post['label'][$key],    
                    'ratio' => $post['ratio'][$key],
                    'capped' => $post['capped'][$key],
                    'is_show' => $post['is_show'][$key],
                ];
            }

            $model->value = json_encode(['info' => $info]);
            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }
        }

        $info = ArrayHelper::getValue($data, 'info', []);
        $defaultChecked = ArrayHelper::getValue($data, 'default_checked');
        $isModify = ArrayHelper::getValue($data, 'is_modify', true);


//        $data = [
//            'info' => [
//                [],
//                [],
//                [],
//            ],
//            'default_checked' => '',
//            'is_modify' => true
//        ];


        return $this->render('rate', ['info' => $info, 'defaultChecked' => $defaultChecked, 'isModify' => $isModify]);
    }
}