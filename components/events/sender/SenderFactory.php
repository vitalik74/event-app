<?php

namespace app\components\events\sender;


use app\components\events\Event;
use app\components\events\EventModelInterface;
use ReflectionClass;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\web\IdentityInterface;

class SenderFactory extends Object
{
    /**
     * @param Component $sender
     * @param EventModelInterface $eventModel
     * @param $eventClass
     * @param IdentityInterface $user
     * @param null $data
     * @throws InvalidConfigException
     */
    public static function create(Component $sender, EventModelInterface $eventModel, $eventClass, IdentityInterface $user, $data = null)
    {
        $reflection = new ReflectionClass($eventClass);

        if (!$reflection->implementsInterface(__NAMESPACE__ . '\SenderInterface')) {
            throw new InvalidConfigException('The "' . $eventClass . '" must be implements from "SenderInterface"');
        }

        /** @var SenderInterface $sender */
        $sender = new $eventClass([
            'name' => strtolower($reflection->getName()),
            'sender' => $sender,
            'data' => $data,
            'eventModel' => $eventModel,
            'user' => $user
        ]);
        $sender->send();
    }
}