<?php

use yii\db\Migration;

class m260507_021326_create_table_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'account_id' => $this->integer(),
            'content' => $this->text()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-comment-post-id',
            'comment',
            'post_id',
            'post',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-comment-account-id',
            'comment',
            'account_id',
            'account',
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
        $this->dropForeignKey('fk-comment-post-id', 'comment');
        $this->dropForeignKey('fk-comment-account-id', 'comment');
        $this->dropTable('comment');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    public function down()
    {
        echo "m260507_021326_create_table_comment cannot be reverted.\n";

        return false;
    }
    */
}
