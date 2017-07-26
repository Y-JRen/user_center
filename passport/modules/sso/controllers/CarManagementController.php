<?php

namespace passport\modules\sso\controllers;

use passport\controllers\AuthController;
use passport\helpers\Config;
use passport\modules\sso\models\CarManagement;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

/**
 * CarManagementController implements the CRUD actions for CarManagement model.
 */
class CarManagementController extends AuthController
{
    /**
     * Lists all CarManagement models.
     * @return mixed
     */
    public function actionList()
    {
        $query = CarManagement::find()->where([
            'uid' => Yii::$app->user->getId(),
            'status' => CarManagement::STATUS_SHOW
        ]);

        $data = new ActiveDataProvider([
            'query' => $query,
        ]);

        $pagination = new Pagination(['totalCount' => $query->count()]);

        return $this->_return([
            'list' => $data->getModels(),
            'pages' => [
                'totalCount' => intval($pagination->totalCount),
                'pageCount' => $pagination->getPageCount(),
                'currentPage' => $pagination->getPage() + 1,
                'perPage' => $pagination->getPageSize(),
            ]
        ]);
    }

    /**
     * Displays a single CarManagement model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->_return($this->findModel($id));
    }

    /**
     * Creates a new CarManagement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CarManagement();
        $model->status = CarManagement::STATUS_SHOW;
        $model->uid = Yii::$app->user->id;
        $model->platform = Config::getPlatform();

        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return $this->_return('添加成功');
        } else {
            Yii::error(json_encode($model->getErrors()));
            return $this->_error(1102, current($model->getFirstErrors()));
        }
    }

    /**
     * Updates an existing CarManagement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(CarManagement::SCENARIO_UPDATE);

        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return $this->_return('更新成功');
        } else {
            Yii::error(json_encode($model->getErrors()));
            return $this->_error(1102, current($model->getFirstErrors()));
        }
    }

    /**
     * Deletes an existing CarManagement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(CarManagement::SCENARIO_DELETE);
        $model->status = CarManagement::STATUS_DELETE;
        if ($model->save()) {
            return $this->_return('删除成功');
        } else {
            Yii::error(json_encode($model->getErrors()));
            return $this->_error(1102, current($model->getFirstErrors()));
        }
    }

    /**
     * Finds the CarManagement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CarManagement the loaded model
     * @throws InvalidParamException if the model cannot be found
     */
    protected function findModel($id)
    {
        /* @var $model CarManagement */
        if (YII_ENV_DEV) {
            $model = CarManagement::find()->where(['id' => $id])->one();
        } else {
            $model = CarManagement::find()->where(['id' => $id, 'uid' => Yii::$app->user->getId(), 'status' => CarManagement::STATUS_SHOW])->one();
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new InvalidParamException('传递参数有误', 1101);
        }
    }
}
