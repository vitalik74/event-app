<?php

namespace tests\codeception\unit\models;


use app\models\Event;
use app\tests\codeception\unit\fixtures\EventFixture;
use Codeception\Specify;
use yii\codeception\DbTestCase;

class EventTest extends DbTestCase
{
    use Specify;

    public function fixtures()
    {
        return [
            'events' => EventFixture::className(),
        ];
    }

    public function testValidateWrong()
    {
        $model = new Event();

        $this->specify('event wrong', function () use ($model) {
            expect('model should not validate', $model->validate())->false();

            expect('error message should be set', $model->errors)->hasKey('type');
            expect('error message should be set', $model->errors)->hasKey('name');
            expect('error message should be set', $model->errors)->hasKey('event');
            expect('error message should be set', $model->errors)->hasKey('title');
        });
    }

    public function testValidateCorrect()
    {
        $model = new Event([
            'type' => 'Type',
            'name' => 'Name',
            'event' => 'Event',
            'title' => 'Title'
        ]);

        $this->specify('event wrong', function () use ($model) {
            expect('model should not validate', $model->validate())->true();
        });
    }
}