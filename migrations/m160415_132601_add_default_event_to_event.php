<?php

use yii\db\Migration;

/**
 * Handles adding default_event to table `event`.
 */
class m160415_132601_add_default_event_to_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{%event}}', 'default_event', $this->string(100));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{%event}}', 'default_event');
    }
}
