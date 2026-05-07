<?php

use yii\db\Migration;

class m260507_064523_create_table_role_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->createTable('role_permission', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer()->notNull(),
            'permission_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        // Add foreign key for role_id
        $this->addForeignKey(
            'fk-role_permission-role_id',
            'role_permission',
            'role_id',
            'role',
            'id',
            'CASCADE'
        );

        // Add foreign key for permission_id
        $this->addForeignKey(
            'fk-role_permission-permission_id',
            'role_permission',
            'permission_id',
            'permission',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('role_permission');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260507_064523_create_table_role_permission cannot be reverted.\n";

        return false;
    }
    */
}
