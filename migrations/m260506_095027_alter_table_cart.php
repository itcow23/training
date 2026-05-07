<?php

use yii\db\Migration;

class m260506_095027_alter_table_cart extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-cart-product-id','cart');
        $this->dropColumn('cart', 'product_id');
        $this->dropColumn('cart', 'quantity');
        $this->dropColumn('cart', 'total'); 
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('cart', 'product_id', $this->integer()->notNull()->after('account_id'));
        $this->addColumn('cart', 'quantity', $this->integer()->notNull()->after('product_id'));
        $this->addColumn('cart', 'total', $this->double()->notNull()->after('quantity'));

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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260506_095027_alter_table_cart cannot be reverted.\n";

        return false;
    }
    */
}
