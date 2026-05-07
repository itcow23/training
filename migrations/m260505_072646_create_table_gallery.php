<?php

use yii\db\Migration;

class m260505_072646_create_table_gallery extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('gallery', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'img' => $this->string()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-gallery-product-id',
            'gallery',
            'product_id',
            'product',
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
        $this->dropForeignKey('fk-gallery-product-id','gallery');
        $this->dropTable('gallery');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260505_072646_create_table_gallery cannot be reverted.\n";

        return false;
    }
    */
}
