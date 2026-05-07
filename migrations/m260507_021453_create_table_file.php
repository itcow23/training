<?php

use yii\db\Migration;

class m260507_021453_create_table_file extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('file', [
            'id' => $this->primaryKey(),
            'file_id' => $this->integer()->notNull(),
            'file_type' => $this->string()->notNull(),
            'filepath' => $this->string()->notNull(),
            'caption' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('file');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_021453_create_table_file cannot be reverted.\n";

        return false;
    }
    */
}
