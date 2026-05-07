<?php

use yii\db\Migration;

class m260505_071228_create_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order', [
            'id' => $this->string()->notNull()->unique(),
            'account_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'total' => $this->integer()->notNull(),
            'pay' => $this->integer()->defaultValue(1),
            'status' => $this->integer()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'PRIMARY KEY(id)'
        ]);

        $this->addForeignKey(
            'fk-order-account-id',
            'order',
            'account_id',
            'account',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order-product-id',
            'order',
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
       $this->dropForeignKey('fk-order-account-id','order');
        $this->dropForeignKey('fk-order-account-id','order');
        $this->dropTable('order');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260505_071228_create_table_order cannot be reverted.\n";

        return false;
    }
    */
}
