<?php

namespace console\migrations\operation;

use yii\db\Migration;

class M170414104514CreateNewsTable extends Migration
{
    private $table = 'news';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->table, [
           'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->defaultValue('')->comment('标题'),
            'sub_title' => $this->string()->notNull()->defaultValue('')->comment('副标题'),
            'content' => $this->string()->notNull()->defaultValue('')->comment('内容'),
            'image_url' => $this->string(1024)->notNull()->defaultValue('')->comment('新闻图URL'),
            'go_url' => $this->string()->notNull()->defaultValue('')->comment('新闻跳转链接'),
            'type' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        echo "M170414104514CreateNewsTable cannot be reverted.\n";

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
