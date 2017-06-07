<?php
/**
 * Created by PhpStorm.
 * User: xiongjun
 * Date: 2017/6/6
 * Time: 09:43
 */

namespace passport\modules\pay\forms;


use common\models\Order;
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
            ['order_subtype', 'in','range' => [1,2,3,'alipay_pc', 'alipay_wap']],
        ];
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord){
            $this->platform = Config::getPlatform();
            $this->order_id = Config::createOrderId();
        }
        return parent::beforeSave($insert);
    }
}