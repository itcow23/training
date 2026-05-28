<?php

use yii\db\Migration;

class m260528_074233_add_product_name_to_order_items extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_items}}', 'product_name', $this->string(255)->null());
        
        // Backfill existing order items by copying product names from the products table
        try {
            $this->execute("UPDATE {{%order_items}} oi INNER JOIN {{%products}} p ON oi.product_id = p.id SET oi.product_name = p.name");
        } catch (\Exception $e) {
            // Graceful in case of raw sql dialect issues, though standard for MySQL in Laragon
        }
    }

    public function safeDown()
    {
        $this->dropColumn('{{%order_items}}', 'product_name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260528_074233_add_product_name_to_order_items cannot be reverted.\n";

        return false;
    }
    */
}
