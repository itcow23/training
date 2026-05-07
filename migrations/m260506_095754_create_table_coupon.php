<?php

use yii\db\Migration;

class m260506_095754_create_table_coupon extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('coupon', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull()->unique(),
            'type' => $this->string()->notNull(),
            'value' => $this->decimal(10,2)->notNull(),
            'max_amount' => $this->decimal(10,2)->notNull(),
            'min_purchase' => $this->decimal(10,2)->notNull(),
            'usage_limit' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(1),
            'start_date' => $this->dateTime()->notNull(),
            'expiry_date' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('coupon');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260506_095754_create_table_coupon cannot be reverted.\n";

        return false;
    }
    */
}
