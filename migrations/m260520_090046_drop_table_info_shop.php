<?php

use yii\db\Migration;

class m260520_090046_drop_table_info_shop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('info_shop');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('info_shop', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'address' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_090046_drop_table_info_shop cannot be reverted.\n";

        return false;
    }
    */
}
