<?php

namespace console\migrations\tag;

use yii\db\Migration;

class M170414104555CreateTagUserRTable extends Migration
{
    private $table = 'tag_user_r';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->table, [
            'tag_id' => $this->integer(),
            'user_id' => $this->integer(),
            'created_at' => $this->timestamp()->notNull(),
            'PRIMARY KEY(tag_id, user_id)'
        ], $tableOptions);
    }

    public function down()
    {
        echo "M170414104555CreateTagUserRTable cannot be reverted.\n";

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
