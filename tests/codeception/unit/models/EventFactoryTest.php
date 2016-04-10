<?php

namespace tests\codeception\unit\models;


use app\components\events\EventFactory;
use yii\codeception\TestCase;
use Codeception\Specify;


class EventFactoryTest extends TestCase
{
    use Specify;

    public function testInit()
    {
        $event = new EventFactory([
            'app\models'
        ]);

        $this->specify('event models correct', function () use ($event) {
            expect('is array', is_array($event->getEventsFromModels()))->true();

            //expect('model User is set', $event->getEventsFromModels())->has;
        });
    }
}