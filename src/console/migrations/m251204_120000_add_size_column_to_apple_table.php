<?php

use yii\db\Migration;

/**
 * Handles adding size column to table `{{%apples}}`.
 */
class m251204_120000_add_size_column_to_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%apples}}', 'size', $this->integer()->notNull()->defaultValue(10));
        $this->alterColumn('{{%apples}}', 'eaten_percent', $this->decimal(5,2)->notNull()->defaultValue(0));
        $this->alterColumn('{{%apples}}', 'created_at', $this->timestamp()->notNull());
        $this->alterColumn('{{%apples}}', 'fallen_at', $this->timestamp()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%apples}}', 'eaten_percent', $this->integer()->notNull()->defaultValue(0));
        $this->dropColumn('{{%apples}}', 'size');
        $this->alterColumn('{{%apples}}', 'created_at', $this->integer()->notNull());
        $this->alterColumn('{{%apples}}', 'fallen_at', $this->integer()->null());        
    }
}