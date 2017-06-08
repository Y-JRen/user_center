<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 09:43
 */

namespace passport\modules\pay\forms;


use common\models\Order;
use common\models\UserBalance;
use common\models\UserFreeze;
use passport\helpers\Config;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;

/**
 * 订单表单
 *
 * Class OrderForm
 * @package passport\modules\pay\forms
 */
class OrderForm extends Order
{

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_type', 'amount', 'status'], 'required'],
            [['uid', 'order_type', 'status', 'notice_status', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['platform_order_id', 'order_id'], 'string', 'max' => 30],
            [['order_subtype', 'desc', 'notice_platform_param', 'remark'], 'string', 'max' => 255],
            ['order_id', 'unique'],
            ['order_type','in', 'range' => [1,2,3,4]],
            ['order_subtype', 'in','range' => ['wechat_code', 'wechat_jsapi','alipay_pc', 'alipay_wap']],
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if($this->isNewRecord){
            $this->platform = Config::getPlatform();
            $this->order_id = Config::createOrderId();
        }
        return parent::beforeSave($insert);
    }

    /**
     *  消费订单
     *
     * @return bool
     * @throws Exception
     */
    public function consumeSave()
    {
        $userBalance = UserBalance::findOne($this->uid);
        if ($userBalance->amount < $this->amount) {
            $this->addError('amount', '余额不足');
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            if (!$this->save()) {
                throw new Exception('订单生成失败');
            }
            $userBalance->amount -= $this->amount;
            $userBalance->updated_at = time();
            if (!$userBalance->save()) {
                throw new Exception('余额扣除失败');
            }
            $userFreeze = UserFreeze::findOne($this->uid);
            if(empty($userFreeze)) {
                $userFreeze = new UserFreeze();
            }
            $userFreeze->amount += $this->amount;
            $userFreeze->updated_at = time();
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}