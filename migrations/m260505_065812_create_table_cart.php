<?php

use yii\db\Migration;

class m260505_065812_create_table_cart extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'total' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-cart-account-id',
            'cart',
            'account_id',
            'account',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-cart-product-id',
            'cart',
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
        $this->dropForeignKey('fk-cart-account-id','cart');
        $this->dropForeignKey('fk-cart-account-id','cart');
        $this->dropTable('cart');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260505_065812_create_table_cart cannot be reverted.\n";

        return false;
    }
    */
}
