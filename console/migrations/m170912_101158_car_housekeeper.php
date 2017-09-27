<?php

use common\models\CarManagement;
use yii\db\Migration;

/**
 * Class m170912_101158_car_housekeeper
 * 添加车管家表
 * 添加车辆的行驶证字段
 */
class m170912_101158_car_housekeeper extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB AUTO_INCREMENT=881130';
        }

        $this->createTable('{{%car_housekeeper}}', [
            'id' => $this->primaryKey()->notNull(),
            'uid' => $this->integer()->notNull(),
            'terminal_no' => $this->string(32)->unique()->notNull()->comment('终端序列号'),
            'car_management_id' => $this->integer()->notNull(),
            'client_device_no' => $this->string(32)->notNull()->defaultValue('')->comment('客户端设备号'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->notNull()->comment('最后更新时间'),
        ], $tableOptions);

        $this->addColumn(CarManagement::tableName(), 'driving_license', $this->string(1000)->notNull()->defaultValue('')->comment('行驶证'));
    }

    public function safeDown()
    {
        $this->dropColumn(CarManagement::tableName(), 'driving_license');
        $this->dropTable('{{%car_housekeeper}}');
    }
}
