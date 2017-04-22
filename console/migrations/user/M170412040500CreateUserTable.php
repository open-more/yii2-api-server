<?php

namespace console\migrations\user;

use yii\db\Expression;
use yii\db\Migration;

class M170412040500CreateUserTable extends Migration
{
    private $tableName = 'user';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'mobile' => $this->string(32)->notNull()->defaultValue('')->comment('手机号'),
            'nickname' => $this->string(32)->notNull()->defaultValue('')->comment('昵称'),
            'avatar' => $this->string(1024)->notNull()->defaultValue('')->comment('头像'),
            'occupation' => $this->string()->notNull()->defaultValue('')->comment('工作'),
            'birthday' => $this->date()->comment('生日'),
            'gender' => $this->smallInteger()->notNull()->defaultValue(0)->comment('性别 0:未知 1:男 2:女'),
            'location' => $this->string()->notNull()->defaultValue('')->comment('地区'),
            'is_test' => $this->smallInteger()->notNull()->defaultValue(0)->comment('是否测试用户 0:否 1:是'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态 10:激活用户 20:禁止用户'),
            'created_at' => $this->timestamp()->notNull()->defaultValue(0)->comment('创建时间即注册时间'),
            'updated_at' => $this->timestamp()->notNull()->defaultValue(0)->comment('记录更新时间'),
            'logined_at' =>  $this->timestamp()->notNull()->defaultValue(0)->comment('上次登录时间'),
        ], $tableOptions);

        $this->createIndex('idx-user-mobile', $this->tableName, [
            'mobile',
        ]);

    }

    public function down()
    {
        $this->dropTable($this->tableName);

        return true;
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
