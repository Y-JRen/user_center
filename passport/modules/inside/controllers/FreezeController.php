<?php
/**
 * Created by PhpStorm.
 * User: legend
 * Date: 2017/9/13
 * Time: 上午11:17
 */

namespace passport\modules\inside\controllers;


use common\traits\FreezeTrait;
use Exception;
use passport\modules\inside\models\FreezeRecord;
use passport\modules\inside\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class FreezeController extends BaseController
{
    use FreezeTrait;

    public function verbs()
    {
        return [
            'user-info' => ['GET'],
            'order-info' => ['GET'],
            'thaw' => ['POST'],
        ];
    }

    /**
     * 获取用户冻结信息通过uid
     * @param $uid
     * @return array
     */
    public function actionUserInfo($uid)
    {
        $model = FreezeRecord::find()->where(['uid' => $uid, 'status' => FreezeRecord::STATUS_FREEZE_OK])->all();

        return $this->_return($model);
    }

    /**
     * 获取用户冻结信息通过order_no
     * @param $order_no
     * @return array
     */
    public function actionOrderInfo($order_no)
    {
        $model = FreezeRecord::find()->where(['order_no' => $order_no, 'status' => FreezeRecord::STATUS_FREEZE_OK])->one();
        if ($model) {
            $model->setScenario('view');
        }
        return $this->_return($model);
    }

    /**
     * 获取用户冻结记录列表
     * @return array
     */
    public function actionList()
    {
        $phone = Yii::$app->request->get('phone');

        $query = FreezeRecord::find();
        if (!empty($phone)) {
            $user = User::find()->where(['phone' => $phone])->asArray()->one();
            $query->andWhere(['uid' => ArrayHelper::getValue($user, 'id')]);
        }

        $data = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => min(max(Yii::$app->request->get('pageSize', 20), 1), 50)
            ],
        ]);


        return $this->_return([
            'list' => $data->getModels(),
            'pages' => [
                'totalCount' => intval($data->pagination->totalCount),
                'pageCount' => $data->pagination->pageCount,
                'currentPage' => $data->pagination->page + 1,
                'perPage' => $data->pagination->pageSize,
            ]
        ]);
    }

    /**
     * 解冻
     * @return array
     */
    public function actionThaw()
    {
        $order_no = Yii::$app->request->post('order_no');
        $uid = Yii::$app->request->post('uid');
        $amount = Yii::$app->request->post('amount');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->thaw($order_no, $uid, $amount);

            $transaction->commit();

            return $this->_return(true);
        } catch (Exception $e) {
            $transaction->rollBack();
            return $this->_return(false, 3002, $e->getMessage());
        }
    }
}