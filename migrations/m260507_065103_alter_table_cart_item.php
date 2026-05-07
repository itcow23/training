<?php

use yii\db\Migration;

class m260507_065103_alter_table_cart_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cart_item', 'product_price', $this->double()->after('quantity'));
        $this->addColumn('cart_item', 'total_price', $this->double()->after('product_price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cart_item', 'product_price');
        $this->dropColumn('cart_item', 'total_price');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_065103_alter_table_cart_item cannot be reverted.\n";

        return false;
    }
    */
}
