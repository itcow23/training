<?php

use yii\db\Migration;

class m260506_100921_alter_table_order_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->dropColumn('order_detail', 'name');
            $this->dropColumn('order_detail', 'phone');
            $this->dropColumn('order_detail', 'address');
            $this->addColumn('order_detail', 'product_id', $this->integer()->notNull()->after('order_id'));
            $this->addColumn('order_detail', 'quantity', $this->integer()->notNull()->after('product_id'));
            $this->addColumn('order_detail', 'total_price', $this->decimal(10,2)->notNull()->after('quantity'));
            $this->addForeignKey(
                'fk-order_detail-product_id',
                'order_detail',
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
            $this->dropForeignKey('fk-order_detail-product_id', 'order_detail');
            $this->dropColumn('order_detail', 'product_id');
            $this->dropColumn('order_detail', 'quantity');
            $this->dropColumn('order_detail', 'total_price');
            $this->addColumn('order_detail', 'name', $this->string()->notNull()->after('order_id'));
            $this->addColumn('order_detail', 'phone', $this->string()->notNull()->after('name'));
            $this->addColumn('order_detail', 'address', $this->text()->notNull()->after('phone'));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260506_100921_alter_table_order_detail cannot be reverted.\n";

        return false;
    }
    */
}
