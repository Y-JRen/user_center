<?php

use common\models\SystemConf;
use yii\db\Migration;

class m170904_060729_add_column_to_system_conf extends Migration
{
    public function safeUp()
    {
        $columns = [
            'key' => 'pre_order_valid_time',
            'label' => '预处理订单有效时间(天数)',
            'value' => 7,
            'type' => 'text',
            'remark' => '单位：天数',
            'is_show' => 0
        ];
        $this->insert(SystemConf::tableName(), $columns);
    }

    public function safeDown()
    {
        $this->delete(SystemConf::tableName(), ['key' => 'pre_order_valid_time']);
    }
}
