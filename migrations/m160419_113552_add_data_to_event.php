<?php

use yii\db\Migration;

/**
 * Handles adding data to table `event`.
 */
class m160419_113552_add_data_to_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $values = [
            [
                'name' => 'Delault before insert',
                'event' => 'app\models\Article||beforeInsert',
                'user_id' => null,
                'title' => 'Статья {articleName} добавлена!',
                'text' => 'Читайте нашу статью {articleName} прямо сейчас!

Вот краткий отрывок из неё:
{articleShortText}',
                'type' => '["email","browser"]',
                'default_event' => ''
            ],
            [
                'name' => 'Custom event with param',
                'event' => 'app\models\Article||sendUsersParam',
                'user_id' => null,
                'title' => 'Custom event with param {articleName}',
                'text' => 'Поля:

{articleId}
{articleName}
{articleText}',
                'type' => '["email","browser"]',
                'default_event' => '["yii\\\\db\\\\ActiveRecord||afterInsert","yii\\\\db\\\\ActiveRecord||afterUpdate"]'
            ],
            [
                'name' => 'Custom with related models',
                'event' => 'app\models\Article||sendUsersModels',
                'user_id' => null,
                'title' => 'Событие с моделями {articleName} {userUsername}',
                'text' => 'Доступные поля:
{articleId}
{articleName}
{articleText}
{userId}
{userUsername}',
                'type' => '["email","browser"]',
                'default_event' => '["yii\\\\db\\\\ActiveRecord||beforeInsert"]'
            ],
            [
                'name' => 'Event with closure',
                'event' => 'app\models\Article||sendUsersClosure',
                'user_id' => null,
                'title' => '{closureTest} начали!',
                'text' => 'Доступные поля:
{articleName}
{articleText}
{closureTest}
{closureTest2}',
                'type' => '["email"]',
                'default_event' => '["yii\\\\db\\\\ActiveRecord||afterInsert"]'
            ],
        ];


        foreach ($values as $value) {
            $this->insert('{{%event}}', $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->truncateTable('{{%event}}');
    }
}
