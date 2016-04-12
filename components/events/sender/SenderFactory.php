<?php

namespace app\components\events\sender;


use app\components\events\Event;
use app\components\events\EventModelInterface;
use ReflectionClass;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Object;

class SenderFactory extends Object
{
    /**
     * @param Component $sender
     * @param EventModelInterface $eventModel
     * @param $name
     * @param null $data
     * @throws InvalidConfigException
     */
    public static function create(Component $sender, EventModelInterface $eventModel, $name, $data = null)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name);
        $reflection = new ReflectionClass($class);

        if (!$reflection->implementsInterface(__NAMESPACE__ . '\SenderInterface')) {
            throw new InvalidConfigException('The "' . $class . '" must be implements from "SenderInterface"');
        }

        /** @var SenderInterface $sender */
        $sender = new $class([
            'name' => $name,
            'sender' => $sender,
            'data' => $data,
            'eventModel' => $eventModel
        ]);
        $sender->send();
    }
}