<?php

use yii\db\Migration;

class m260506_095443_create_table_cart_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cart_item', [
            'id' => $this->primaryKey(),
            'cart_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-cart_item-cart_id',
            'cart_item',
            'cart_id',
            'cart',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-cart_item-product_id',
            'cart_item',
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
        $this->dropForeignKey('fk-cart_item-cart_id','cart_item');
        $this->dropForeignKey('fk-cart_item-product_id','cart_item');
        $this->dropTable('cart_item');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260506_095443_create_table_cart_item cannot be reverted.\n";

        return false;
    }
    */
}
