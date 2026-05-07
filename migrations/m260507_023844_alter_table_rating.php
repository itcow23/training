<?php

use yii\db\Migration;

class m260507_023844_alter_table_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-rating-account-post-id', 'rating', ['account_id', 'post_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-rating-account-post-id', 'rating');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_023844_alter_table_rating cannot be reverted.\n";

        return false;
    }
    */
}
