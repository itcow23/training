<?php

use yii\db\Migration;

class m260507_021247_create_table_post_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_product', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-post_product-post-id',
            'post_product',
            'post_id',
            'post',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-post_product-product-id',
            'post_product',
            'product_id',
            'product',
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
        $this->dropForeignKey('fk-post_product-post-id', 'post_product');
        $this->dropForeignKey('fk-post_product-product-id', 'post_product');
        $this->dropTable('post_product');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    public function down()
    {
        echo "m260507_021247_create_table_post_product cannot be reverted.\n";

        return false;
    }
    */
}
