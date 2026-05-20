<?php

use yii\db\Migration;

class m260520_085810_drop_column_img_table_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('category', 'img');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('category', 'img', $this->string()->null());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_085810_drop_column_img_table_category cannot be reverted.\n";

        return false;
    }
    */
}
