<?php

use yii\db\Migration;

class m260507_065312_rename_table_file extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('file', 'media');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('media', 'file');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_065312_rename_table_file cannot be reverted.\n";

        return false;
    }
    */
}
