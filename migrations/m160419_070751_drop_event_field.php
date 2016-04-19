<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `event_field`.
 */
class m160419_070751_drop_event_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('event_field');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('event_field', [
            'id' => $this->primaryKey(),
        ]);
    }
}
