<?php

use yii\db\Migration;

class m260506_093320_alter_table_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'membership_level_id', $this->integer()->after('role_id'));
        $this->addColumn('account', 'total_point', $this->decimal(10,2)->defaultValue(0)->after('membership_level_id'));
        $this->addColumn('account', 'status', $this->integer()->defaultValue(1)->after('password'));

        $this->addForeignKey(
            'fk-account-membership_level_id',
            'account',
            'membership_level_id',
            'membership_level',
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
        $this->dropForeignKey('fk-account-membership_level_id', 'account');
        $this->dropColumn('account', 'membership_level_id');
        $this->dropColumn('account', 'total_point');
        $this->dropColumn('account', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }
    public function down()
    {
        echo "m260506_093320_alter_table_account cannot be reverted.\n";

        return false;
    }
    */
}
