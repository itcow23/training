<?php

use yii\db\Migration;

class m260506_100035_alter_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-order-product-id','order');
        $this->dropColumn('order', 'product_id');
        $this->dropColumn('order', 'quantity');
        $this->dropColumn('order', 'total');
        $this->addColumn('order','membership_level_id', $this->integer()->null()->after('account_id'));
        $this->addColumn('order','name', $this->string()->notNull()->after('membership_level_id'));
        $this->addColumn('order','email', $this->string()->notNull()->after('name'));
        $this->addColumn('order','phone', $this->text()->notNull()->after('email'));
        $this->addColumn('order','address', $this->text()->notNull()->after('phone'));
        $this->addColumn('order','discount_amount', $this->double(10,2)->defaultValue(0.00)->after('address'));
      
    }

     
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'membership_level_id');
        $this->dropColumn('order', 'name');
        $this->dropColumn('order', 'email');
        $this->dropColumn('order', 'phone');
        $this->dropColumn('order', 'address');
        $this->dropColumn('order', 'discount_amount');
        $this->addColumn('order', 'product_id', $this->integer()->notNull()->after('account_id'));
        $this->addColumn('order', 'quantity', $this->integer()->notNull()->after('product_id'));
        $this->addColumn('order', 'total', $this->double()->notNull()->after('quantity'));

        $this->addForeignKey(
            'fk-order-product_id',
            'order',
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
        echo "m260506_100035_alter_table_order cannot be reverted.\n";

        return false;
    }
    */
}
