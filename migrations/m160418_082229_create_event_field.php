<?php

use yii\db\Migration;

/**
 * Handles the creation for table `event_field`.
 */
class m160418_082229_create_event_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('event_field', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull(),
            'value' => $this->string(100)->notNull(),
            'event_id' => $this->integer()->notNull()
        ]);

        $this->addForeignKey('fx_event_field_event_id', '{{%event_field}}', 'event_id', '{{%event}}', 'id', 'CASCADE', 'NO ACTION');

        $this->createIndex('index_event_field_event_id_type', '{{%event_field}}', ['event_id', 'type']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('event_field');
    }
}
