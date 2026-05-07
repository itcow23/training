<?php

use yii\db\Migration;

class m260507_064744_alter_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'subtotal', $this->double()->after('discount_amount'));
        $this->addColumn('order', 'final_total', $this->double()->after('subtotal'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'subtotal');
        $this->dropColumn('order', 'final_total');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {


    public function down()
    {
        echo "m260507_064744_alter_table_order cannot be reverted.\n";

        return false;
    }
    */
}
