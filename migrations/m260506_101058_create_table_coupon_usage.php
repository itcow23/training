<?php

use yii\db\Migration;

class m260506_101058_create_table_coupon_usage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('coupon_usage', [
            'id' => $this->primaryKey(),
            'coupon_id' => $this->integer()->notNull(),
            'order_id' => $this->string()->notNull(),
            'account_id' => $this->integer()->notNull(),
            'applied_code' => $this->string()->notNull(),
            'applied_value' => $this->decimal(10,2)->notNull(),
            'applied_type' => $this->string()->notNull(),
            'applied_max_amount' => $this->decimal(10,2)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-coupon-usage-coupon-id',
            'coupon_usage',
            'coupon_id',
            'coupon',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-coupon-usage-order-id',
            'coupon_usage',
            'order_id',
            'order',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-coupon-usage-account-id',
            'coupon_usage',
            'account_id',
            'account',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-coupon-usage-coupon-id','coupon_usage');
        $this->dropForeignKey('fk-coupon-usage-order-id','coupon_usage');
        $this->dropForeignKey('fk-coupon-usage-account-id','coupon_usage');
        $this->dropTable('coupon_usage');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {


    public function down()
    {
        echo "m260506_101058_create_table_coupon_usage cannot be reverted.\n";

        return false;
    }
    */
}
