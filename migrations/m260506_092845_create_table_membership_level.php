<?php

use yii\db\Migration;

class m260506_092845_create_table_membership_level extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('membership_level', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'point_required' => $this->decimal(10,2)->notNull(),
            'discount_rate' => $this->decimal(5,2)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('membership_level');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260506_092845_create_table_membership_level cannot be reverted.\n";

        return false;
    }
    */
}
