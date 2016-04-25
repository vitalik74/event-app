<?php

use yii\db\Migration;

/**
 * Handles the creation for table `browser_event`.
 */
class m160414_061936_create_browser_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('browser_event', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'text' => $this->text()->notNull(),
            'from_user_id' => $this->integer()->notNull(),
            'viewed' => $this->boolean()->defaultValue(false),
            'to_user_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('index_browser_event_from_user_id', '{{%browser_event}}', 'from_user_id');

        $this->addForeignKey('fx_browser_event_from_user_id', '{{%browser_event}}', 'from_user_id', '{{%user}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fx_browser_event_to_user_id', '{{%browser_event}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'NO ACTION');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('browser_event');
    }
}
