<?php

use yii\db\Migration;

class m260506_093837_alter_table_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product', 'status', $this->integer()->defaultValue(1)->after('price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'status');
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
        echo "m260506_093837_alter_table_product cannot be reverted.\n";

        return false;
    }
    */
}
