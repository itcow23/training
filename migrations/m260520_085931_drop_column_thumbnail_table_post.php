<?php

use yii\db\Migration;

class m260520_085931_drop_column_thumbnail_table_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('post', 'thumbnail');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('post', 'thumbnail', $this->string()->null());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_085931_drop_column_thumbnail_table_post cannot be reverted.\n";

        return false;
    }
    */
}
