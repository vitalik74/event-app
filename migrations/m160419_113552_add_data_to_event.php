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
        $this->execute('
        INSERT INTO `event-app`.event (id, name, event, user_id, title, text, type, default_event) VALUES (\'\', \'Delault before insert\', \'app\\models\\Article||beforeInsert\', null, \'Статья {articleName} добавлена!\', \'Читайте нашу статью {articleName} прямо сейчас!

Вот краткий отрывок из неё:
{articleShortText}\', \'["email","browser"]\', \'""\');
INSERT INTO `event-app`.event (id, name, event, user_id, title, text, type, default_event) VALUES (\'\', \'Custom event with param\', \'app\\models\\Article||sendUsersParam\', null, \'Custom event with param {articleName}\', \'Поля:

{articleId}
{articleName}
{articleText}\', \'["email","browser"]\', \'["yii\\\\db\\\\ActiveRecord||afterInsert","yii\\\\db\\\\ActiveRecord||afterUpdate"]\');
INSERT INTO `event-app`.event (id, name, event, user_id, title, text, type, default_event) VALUES (\'\', \'Costom with related models\', \'app\\models\\Article||sendUsersModels\', null, \'Событие с моделями {articleName} {userUsername}\', \'Доступные поля:
{articleId}
{articleName}
{articleText}
{userId}
{userUsername}\', \'["email","browser"]\', \'["yii\\\\db\\\\ActiveRecord||beforeInsert"]\');
INSERT INTO `event-app`.event (id, name, event, user_id, title, text, type, default_event) VALUES (\'\', \'Event with closure\', \'app\\models\\Article||sendUsersClosure\', null, \'{closureTest} начали!\', \'Доступные поля:
{articleName}
{articleText}
{closureTest}
{closureTest2}\', \'["email"]\', \'["yii\\\\db\\\\ActiveRecord||afterInsert"]\');

        ');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
    }
}
