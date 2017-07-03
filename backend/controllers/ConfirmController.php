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
     * Creates a new RechargeConfirm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RechargeConfirm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RechargeConfirm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RechargeConfirm model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
     * Creates a new RechargeConfirm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateTransfer()
    {
        $model = new TransferConfirm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view2', 'id' => $model->id]);
        } else {
            return $this->render('create2', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing RechargeConfirm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateTransfer($id)
    {
        $model = $this->findModel2($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view2', 'id' => $model->id]);
        } else {
            return $this->render('update2', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing RechargeConfirm model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteTransfer($id)
    {
        $this->findModel2($id)->delete();

        return $this->redirect(['index2']);
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
