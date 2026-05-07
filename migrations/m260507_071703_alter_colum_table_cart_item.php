<?php

use yii\db\Migration;

class m260507_071703_alter_colum_table_cart_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('cart_item', 'product_price', $this->double()->after('quantity')->notNull());
        $this->alterColumn('cart_item', 'total_price', $this->double()->after('product_price')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('cart_item', 'product_price', $this->double()->after('quantity')->null());
        $this->alterColumn('cart_item', 'total_price', $this->double()->after('product_price')->null());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_071703_alter_colum_table_cart_item cannot be reverted.\n";

        return false;
    }
    */
}
