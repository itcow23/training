<?php

use yii\db\Migration;

class m260520_120000_recreate_order_tables extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk-order_detail-product_id', 'order_detail');
        $this->dropForeignKey('fk-order-detail-order-id', 'order_detail');
        $this->dropTable('order_detail');
        $this->dropForeignKey('fk-order-account-id', 'order');
        $this->dropForeignKey('fk-coupon-usage-order-id', 'coupon_usage');
        $this->dropTable('order');

        $this->createTable('orders', [
            'id' => $this->primaryKey(),
            'order_code' => $this->string(20)->notNull()->unique(),
            'account_id' => $this->integer()->notNull(),
            'membership_level_id' => $this->integer(),
            'subtotal' => $this->decimal(10, 2)->notNull(),
            'discount' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'shipping_fee' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'final_total' => $this->decimal(10, 2)->notNull(),
            'currency' => $this->string(3)->notNull()->defaultValue('USD'),
            'pay_method' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'shipping_name' => $this->string(255)->notNull(),
            'shipping_email' => $this->string(255)->notNull(),
            'shipping_phone' => $this->string(255)->notNull(),
            'shipping_address' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-order-account-id',
            'orders',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        $this->createTable('order_items', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'unit_price' => $this->decimal(10, 2)->notNull(),
            'total_price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-order-item-order-id',
            'order_items',
            'order_id',
            'orders',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order-item-product-id',
            'order_items',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-order-item-product-id', 'order_items');
        $this->dropForeignKey('fk-order-item-order-id', 'order_items');
        $this->dropTable('order_items');
        $this->dropForeignKey('fk-order-account-id', 'orders');
        $this->dropTable('orders');

        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull(),
            'membership_level_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'phone' => $this->string(255)->notNull(),
            'address' => $this->string(255)->notNull(),
            'discount_amount' => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'subtotal' => $this->decimal(10, 2)->notNull(),
            'final_total' => $this->decimal(10, 2)->notNull(),
            'pay' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-order-account-id',
            'order',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        $this->createTable('order_detail', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'unit_price' => $this->decimal(10, 2)->notNull(),
            'total_price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-order-detail-order-id',
            'order_detail',
            'order_id',
            'order',
            'id',
            'CASCADE'
        );
    }
}
