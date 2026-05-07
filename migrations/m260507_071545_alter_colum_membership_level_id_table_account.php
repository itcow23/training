<?php

use yii\db\Migration;

class m260507_071545_alter_colum_membership_level_id_table_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('account', 'membership_level_id', $this->integer()->after('role_id')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('account', 'membership_level_id', $this->integer()->after('role_id')->null());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_071545_alter_colum_membership_level_id_table_account cannot be reverted.\n";

        return false;
    }
    */
}
