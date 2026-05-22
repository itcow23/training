<?php

use yii\db\Migration;

class m260522_091310_add_column_avg_rating_table_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('post', 'avg_rating', $this->decimal(3,1)->defaultValue(0)->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('post', 'avg_rating');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260522_091310_add_column_avg_rating_table_post cannot be reverted.\n";

        return false;
    }
    */
}
