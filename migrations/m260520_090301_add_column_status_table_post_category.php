<?php

use yii\db\Migration;

class m260520_090301_add_column_status_table_post_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('post_category', 'status', $this->integer()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('post_category', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_090301_add_column_status_table_post_category cannot be reverted.\n";

        return false;
    }
    */
}
