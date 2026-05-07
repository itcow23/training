<?php

use yii\db\Migration;

class m260505_063740_create_table_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('profile',[
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull(),
            'phone' => $this->string(),
            'address' => $this->string(),
            'img' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey(
            'fk-profile-account-id',
            'profile',
            'account_id',
            'account',
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
       $this->dropForeignKey('fk-profile-account-id','profile');
       $this->dropTable('profile');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo 'm260505_063740_create_table_profile cannot be reverted.\n';

        return false;
    }
    */
}
