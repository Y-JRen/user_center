<?php

namespace backend\controllers;

use common\models\TransferConfirm;
use Yii;
use common\models\RechargeConfirm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RechargeConfirmController implements the CRUD actions for RechargeConfirm model.
 */
class ConfirmController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RechargeConfirm models.
     * @return mixed
     */
    public function actionRecharge()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => RechargeConfirm::find(),
            //对结果按照某种方式进行排序
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RechargeConfirm model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the RechargeConfirm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RechargeConfirm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RechargeConfirm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*以下是transferconfirm部分*/

    /**
     * Lists all RechargeConfirm models.
     * @return mixed
     */
    public function actionTransfer()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TransferConfirm::find(),
            //对结果按照某种方式进行排序
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index2', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RechargeConfirm model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewTransfer($id)
    {
        return $this->render('view2', [
            'model' => $this->findModel2($id),
        ]);
    }

    /**
     * Finds the RechargeConfirm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RechargeConfirm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel2($id)
    {
        if (($model = TransferConfirm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
