<?php

use yii\db\Migration;

class m260507_072022_alter_colum_published_at_table_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('post', 'published_at', $this->dateTime()->after('content')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('post', 'published_at', $this->dateTime()->after('content')->null());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_072022_alter_colum_published_at_table_post cannot be reverted.\n";

        return false;
    }
    */
}
