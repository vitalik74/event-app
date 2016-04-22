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
                'title' => 'Delault before insert - Статья {articleName} добавлена!',
                'text' => 'Читайте нашу статью {articleName} прямо сейчас!<br>

Вот краткий отрывок из неё:<br>
{articleShortText}',
                'type' => '["email","browser"]',
                'default_event' => ''
            ],
            [
                'name' => 'Custom event with param',
                'event' => 'app\models\Article||sendUsersParam',
                'user_id' => null,
                'title' => 'Custom event with param {articleName}',
                'text' => 'Поля:<br>

{articleId}<br>
{articleName}<br>
{articleText}',
                'type' => '["email","browser"]',
                'default_event' => '["afterInsert","afterUpdate"]'
            ],
            [
                'name' => 'Custom with related models',
                'event' => 'app\models\Article||sendUsersModels',
                'user_id' => null,
                'title' => 'Custom with related models - Событие с моделями {articleName} {userUsername}',
                'text' => 'Доступные поля:<br>
{articleId}<br>
{articleName}<br>
{articleText}<br>
{userId}<br>
{userUsername}',
                'type' => '["email","browser"]',
                'default_event' => '["afterInsert"]'
            ],
            [
                'name' => 'Event with closure',
                'event' => 'app\models\Article||sendUsersClosure',
                'user_id' => null,
                'title' => 'Event with closure - {closureTest} начали!',
                'text' => 'Доступные поля:<br>
{articleName}<br>
{articleText}<br>
{closureTest}<br>
{closureTest2}',
                'type' => '["email","browser"]',
                'default_event' => '["afterInsert"]'
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
