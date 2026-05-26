<?php

use yii\db\Migration;

/**
 * Class m260526_104600_add_soft_delete_columns
 */
class m260526_104600_add_soft_delete_columns extends Migration
{
    private array $tables = [
        'category',
        'product',
        'orders',
        'post_category',
        'post',
        'tag',
        'comment',
        'rating'
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->tables as $tableName) {
            $this->addColumn($tableName, 'is_deleted', $this->tinyInteger(1)->notNull()->defaultValue(0));
            $this->addColumn($tableName, 'deleted_at', $this->dateTime()->null()->defaultValue(null));
            $this->createIndex("idx-{$tableName}-is_deleted", $tableName, 'is_deleted');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->tables as $tableName) {
            $this->dropIndex("idx-{$tableName}-is_deleted", $tableName);
            $this->dropColumn($tableName, 'is_deleted');
            $this->dropColumn($tableName, 'deleted_at');
        }
    }
}
