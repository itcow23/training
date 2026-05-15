<?php

use yii\db\Migration;

class m260514_041422_alter_published_at_column_table_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->alterColumn('post', 'published_at', $this->dateTime()->after('content')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('post', 'published_at', $this->dateTime()->after('content')->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260514_041422_alter_published_at_column_table_post cannot be reverted.\n";

        return false;
    }
    */
}
