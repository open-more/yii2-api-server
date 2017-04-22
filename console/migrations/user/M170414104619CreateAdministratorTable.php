<?php

namespace console\migrations\user;

use yii\db\Migration;

class M170414104619CreateAdministratorTable extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('administrator', [
            'id'                 => $this->primaryKey(),
            'account'            => $this->string()->notNull()->defaultValue(0)->comment('登录账号,必须是邮件'),
            'password'       => $this->string()->notNull()->defaultValue('')->comment('密码'),
            'user_name'        => $this->string()->notNull()->defaultValue('')->comment('显示用户名'),
            'type'        => $this->smallInteger()->notNull()->defaultValue(0)->comment('10为超级管理员'),
            'status'     => $this->smallInteger()->notNull()->defaultValue(0)->comment('题图'),
            'created_at'        => $this->timestamp()->notNull(),
            'updated_at'   => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        echo "M170414104619CreateAdministratorTable cannot be reverted.\n";

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
