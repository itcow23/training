<?php

use yii\db\Migration;

class m260520_090032_drop_table_gallery extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('gallery');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('gallery', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'img' => $this->string()->notNull(),
        ]);

        // creates index for column `post_id`
        $this->createIndex(
            'idx-gallery-post_id',
            'gallery',
            'post_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-gallery-post_id',
            'gallery',
            'post_id',
            'post',
            'id',
            'CASCADE'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260520_090032_drop_table_gallery cannot be reverted.\n";

        return false;
    }
    */
}
