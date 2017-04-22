<?php

namespace console\migrations\user;

use yii\db\Migration;

class M170414104632CreateDeviceTable extends Migration
{
    private $table = 'device';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'device_token' => $this->string()->notNull()->defaultValue('')->comment('自己的设备token'),
            'app_version_name' => $this->string()->notNull()->defaultValue('')->comment('当前App的版本名'),
            'channel_code' => $this->string()->notNull()->defaultValue('')->comment('渠道名'),
            'connection_type' => $this->string()->notNull()->defaultValue('')->comment('联网方式'),
            'cpu_info' => $this->string()->notNull()->defaultValue('')->comment('设备CPU'),
            'model_name' => $this->string()->notNull()->defaultValue('')->comment('型号'),
            'location' => $this->string()->notNull()->defaultValue('')->comment('设备位置'),
            'rom_info' => $this->string()->notNull()->defaultValue('')->comment('设备系统信息'),
            'vendor' => $this->string()->notNull()->defaultValue('')->comment('设备厂商'),
            'wifi_mac' => $this->string()->notNull()->defaultValue('')->comment('WIFI地址可为空'),
            'host_name' => $this->string()->notNull()->defaultValue('')->comment('主机名'),
            'push_token' => $this->string()->notNull()->defaultValue('')->comment('第三方推送token'),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        echo "M170414104632CreateDevicesTable cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
