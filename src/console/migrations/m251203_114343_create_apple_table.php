<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apples}}`.
 */
class m251203_114343_create_apple_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apples}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(), 
            'color' => $this->string(50)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'fallen_at' => $this->integer()->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0), // 0 - на дереве, 1 - упало, 2 - гнилое
            'eaten_percent' => $this->integer()->notNull()->defaultValue(0),            
        ]);

        // Добавляем внешний ключ на таблицу user
        $this->addForeignKey(
            'fk-apples-user_id',
            '{{%apples}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apples}}');
    }
}
