<?php

use yii\db\Migration;

class m260505_043138_create_table_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('role',[
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo 'm260505_043138_create_table_role cannot be reverted.\n';

        return false;
    }
    */
}
