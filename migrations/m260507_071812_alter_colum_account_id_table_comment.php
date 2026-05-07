<?php

use yii\db\Migration;

class m260507_071812_alter_colum_account_id_table_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('comment', 'account_id', $this->integer()->after('id')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('comment', 'account_id', $this->integer()->after('id')->null());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_071812_alter_colum_account_id_table_comment cannot be reverted.\n";

        return false;
    }
    */
}
