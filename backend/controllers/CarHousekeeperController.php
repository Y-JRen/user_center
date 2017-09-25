<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/19
 * Time: 上午10:21
 */

namespace backend\controllers;


use backend\models\CarManagement;
use common\helpers\ModelError;
use Yii;
use backend\models\CarHousekeeper;
use yii\web\NotFoundHttpException;

class CarHousekeeperController extends BaseController
{
    public function verbs()
    {
        return [
            'delete' => 'DELETE',
        ];
    }

    public function actionCreate($uid)
    {
        $model = new CarHousekeeper();
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = 'json';
            $result = ['status' => true, 'msg' => ''];
            $managementModel = new CarManagement();
            $managementModel->load(Yii::$app->request->post());
            $managementModel->uid = $uid;
            $managementModel->status = CarManagement::STATUS_SHOW;
            if ($managementModel->save()) {
                $model->load(Yii::$app->request->post());
                $model->uid = $uid;
                $model->car_management_id = $managementModel->id;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '新增成功');
                    return $result;
                } else {
                    $result['msg'] = ModelError::htmlP($model->errors);
                }
            } else {
                $result['msg'] = ModelError::htmlP($managementModel->errors);
            }

            $result['status'] = false;
            return $result;
        }

        return $this->renderAjax('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isPost) {


            Yii::$app->response->format = 'json';
            $result = ['status' => true, 'msg' => ''];
            $managementModel = $model->carManagement;
            if ($managementModel->load(Yii::$app->request->post()) & $managementModel->save()) {
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    Yii::$app->session->setFlash('success', '操作成功');
                    return $result;
                } else {
                    $result['msg'] = ModelError::htmlP($model->errors);
                }
            } else {
                $result['msg'] = ModelError::htmlP($managementModel->errors);
            }
            $result['status'] = false;
            return $result;
        }

        return $this->renderAjax('update', ['model' => $model]);
    }

    /**
     * 删除
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $url = Yii::$app->request->getReferrer();
        $model = $this->findModel($id);
        if ($model && $model->carManagement) {
            $model->carManagement->delete();
            $model->delete();
            Yii::$app->session->setFlash('ok', '删除成功');
        } else {
            Yii::$app->session->setFlash('error', '删除失败');
        }
        return $this->redirect($url);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CarHousekeeper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CarHousekeeper::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}