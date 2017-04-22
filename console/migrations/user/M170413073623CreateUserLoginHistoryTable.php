<?php

namespace console\migrations\user;

use yii\db\Migration;

class M170413073623CreateUserLoginHistoryTable extends Migration
{
    private $table = 'user_login_history';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'client_id' => $this->string()->notNull(),
            'device_id' => $this->integer()->notNull()->defaultValue(0),
            'app_version_name' => $this->string()->notNull()->defaultValue(''),
            'ip' => $this->string()->notNull()->defaultValue(''),
            'created_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        echo "M170413073623CreateUserLoginHistoryTable cannot be reverted.\n";

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
