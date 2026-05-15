<?php

use yii\db\Migration;

class m260514_085403_alter_table_media extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('media','collection', $this->string()->after('filepath')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('media','collection');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260514_085403_alter_table_media cannot be reverted.\n";

        return false;
    }
    */
}
