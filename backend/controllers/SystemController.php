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
        /*echo '<pre>';
        $model = SystemConf::find();
        var_dump($model);die;*/
        $model = SystemConf::find()->where(['key' => 'rate'])->one();
        $data = json_decode(ArrayHelper::getValue($model, 'value', ''), true);

        $defaultChecked = ArrayHelper::getValue($data, 'default_checked',[]);
        $isModify = ArrayHelper::getValue($data, 'is_modify', []);

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

            if(isset($post['default_checked'])){
                $defaultChecked = $post['default_checked'];
            }else{
                $defaultChecked = ArrayHelper::getValue($data, 'default_checked',[]);
            };

            if(isset($post['is_modify'])){
                $isModify = $post['is_modify'];
            }else{
                $isModify = ArrayHelper::getValue($data, 'is_modify', []);
            }

            $model->value = json_encode(
                [
                    'info' => $info,
                    'default_checked' => $defaultChecked,
                    'is_modify' => $isModify
                ]);

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }else{
                //新增一条卡的种类之后将页面重定向到当前页面
                $this->redirect('?r=system/rate');
            }
        }

        $info = ArrayHelper::getValue($data, 'info', []);

        return $this->render('rate', ['info' => $info, 'defaultChecked' => $defaultChecked, 'isModify' => $isModify]);
    }


    //'确认修改'时执行以下代码
    public function actionPsure()
    {
        $model = SystemConf::find()->where(['key' => 'rate'])->one();
        $data = json_decode(ArrayHelper::getValue($model, 'value', ''), true);
        $info = ArrayHelper::getValue($data, 'info', []);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            if(isset($post['default_checked'])){
                $defaultChecked = $post['default_checked'];
            }else{
                $defaultChecked = ArrayHelper::getValue($data, 'default_checked',[]);
            };

            if(isset($post['is_modify'])){
                $isModify = $post['is_modify'];
            }else{
                $isModify = ArrayHelper::getValue($data, 'is_modify', []);
            }

            $model->value = json_encode(
                [
                    'info' => $info,
                    'default_checked' => $defaultChecked,
                    'is_modify' => $isModify
                ]);

            if ($model->update()) {
                $this->redirect('?r=system/rate');
            }else{
                $this->redirect('?r=system/rate');
                /*var_dump($model->errors);
                exit;*/
            }
        }
    }
}