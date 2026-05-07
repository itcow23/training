<?php

use yii\db\Migration;

class m260505_072106_create_table_order_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order_detail', [
            'id' => $this->primaryKey(),
            'order_id' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'address' => $this->string()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-order-detail-order-id',
            'order_detail',
            'order_id',
            'order',
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
       $this->dropForeignKey('fk-order-detail-order-id','order_detail');
       $this->dropTable('order_detail');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260505_072106_create_table_order_detail cannot be reverted.\n";

        return false;
    }
    */
}
