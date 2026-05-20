<?php

use yii\db\Migration;

class m260520_064707_alter_table_post_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-post_product-post_id', 'post_product', ['post_id', 'product_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-post_product-post_id', 'post_product');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_064707_alter_table_post_product cannot be reverted.\n";

        return false;
    }
    */
}
