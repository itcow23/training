<?php

use yii\db\Migration;

class m260507_021130_create_table_post_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_tag', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-post_tag-post-id',
            'post_tag',
            'post_id',
            'post',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-post_tag-tag-id',
            'post_tag',
            'tag_id',
            'tag',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-post_tag-post-id', 'post_tag');
        $this->dropForeignKey('fk-post_tag-tag-id', 'post_tag');
        $this->dropTable('post_tag');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    public function down()
    {
        echo "m260507_021130_create_table_post_tag cannot be reverted.\n";

        return false;
    }
    */
}
