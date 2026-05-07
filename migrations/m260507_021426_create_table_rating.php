<?php

use yii\db\Migration;

class m260507_021426_create_table_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('rating', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'account_id' => $this->integer()->notNull(),
            'score' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-rating-post-id',
            'rating',
            'post_id',
            'post',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-rating-account-id',
            'rating',
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
        $this->dropForeignKey('fk-rating-post-id', 'rating');
        $this->dropForeignKey('fk-rating-account-id', 'rating');
        $this->dropTable('rating');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    public function down()
    {
        echo "m260507_021426_create_table_rating cannot be reverted.\n";

        return false;
    }
    */
}
