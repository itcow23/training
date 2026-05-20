<?php

use yii\db\Migration;

class m260520_042107_add_column_slug_table_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tag', 'slug', $this->string()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tag', 'slug');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_042107_add_column_slug_table_tag cannot be reverted.\n";

        return false;
    }
    */
}
