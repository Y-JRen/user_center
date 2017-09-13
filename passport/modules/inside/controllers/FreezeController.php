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
use Yii;

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
        $model = FreezeRecord::find()->where(['uid' => $uid, 'status' => FreezeRecord::STATUS_FREEZE_OK])->one();

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

        return $this->_return($model);
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