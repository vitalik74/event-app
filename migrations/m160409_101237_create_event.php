<?php

use yii\db\Migration;

/**
 * Handles the creation for table `event`.
 */
class m160409_101237_create_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('event', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60)->notNull(),
            'event' => $this->string(100)->notNull(),
            'user_id' => $this->integer(),
            'title' => $this->string()->notNull(),
            'text' => $this->text(),
            'type' => $this->string(100)->notNull()
        ]);

        $this->createIndex('index_event_user_id', '{{%event}}', 'user_id');
        $this->createIndex('index_event_type', '{{%event}}', 'type');
        $this->createIndex('index_event_event', '{{%event}}', 'event');


        $this->addForeignKey('fx_event_user_id', '{{%event}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'NO ACTION');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('event');
    }
}
