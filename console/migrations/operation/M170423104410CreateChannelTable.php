<?php

namespace console\migrations\operation;

use yii\db\Migration;

class M170423104410CreateChannelTable extends Migration
{
    private $table = 'channel';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->defaultValue(''),
            'description' => $this->string()->notNull()->defaultValue(''),
            'banner' => $this->string()->notNull()->defaultValue(''),
            'icon' => $this->string()->notNull()->defaultValue(''),
            'promotion_image' => $this->string()->notNull()->defaultValue(''),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        echo "M170414104410CreateChannelsTable cannot be reverted.\n";

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
