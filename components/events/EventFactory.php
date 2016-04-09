<?php

namespace components\events;


use ReflectionClass;
use yii\base\Object;

class EventFactory extends Object
{
    const EVENT_EMAIL = 'email';
    const EVENT_BROWSER = 'browser';
    const EVENT_SMS = 'sms';
    const EVENT_GOD = 'God';
    const EVENT_ERROR = 'error';

    public static function getEvents()
    {
        $reflection = new ReflectionClass(__CLASS__);

        return array_flip($reflection->getConstants());
    }
}