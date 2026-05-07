<?php

use yii\db\Migration;

class m260507_020858_create_table_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'content' => $this->text()->notNull(),
            'thumbnail' => $this->string(),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'published_at' => $this->dateTime(),
            'slug' => $this->string()->notNull()->unique(),
            'category_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-post-category-id',
            'post',
            'category_id',
            'post_category',
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
        $this->dropForeignKey('fk-post-category-id', 'post');
        $this->dropTable('post');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    public function down()
    {
        $this->dropForeignKey('fk-post-category-id', 'post');
        $this->dropTable('post');
    }
    */
}
