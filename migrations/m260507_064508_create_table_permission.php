<?php

use yii\db\Migration;

class m260507_064508_create_table_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('permission', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'key' => $this->string()->notNull()->unique(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('permission');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_064508_create_table_permission cannot be reverted.\n";

        return false;
    }
    */
}
