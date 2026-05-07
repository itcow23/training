<?php

use yii\db\Migration;

class m260505_065026_create_table_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('product',[
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'price' => $this->double()->notNull(),
            'description' => $this->text(),
            'discount' => $this->integer(),
            'slug' => $this->string()->unique(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-product-category-id',
            'product',
            'category_id',
            'category',
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
        $this->dropForeignKey('fk-product-category-id','product');
        $this->dropTable('product');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo 'm260505_065026_create_table_product cannot be reverted.\n';

        return false;
    }
    */
}
