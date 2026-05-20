<?php

use yii\db\Migration;

class m260520_064428_alter_table_post_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-post_tag-post_id', 'post_tag', ['post_id', 'tag_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-post_tag-post_id', 'post_tag');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_064428_alter_table_post_tag cannot be reverted.\n";

        return false;
    }
    */
}
