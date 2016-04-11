<?php

namespace app\components\events\sender;


use ReflectionClass;
use yii\base\InvalidConfigException;
use yii\base\Object;

class SenderFactory extends Object
{
    public static function create($name)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name);
        $reflection = new ReflectionClass($class);

        if (!$reflection->implementsInterface(__NAMESPACE__ . '\SenderInterface')) {
            throw new InvalidConfigException('The "' . $class . '" must be implements from "SenderInterface"');
        }

        /** @var SenderInterface $sender */
        $sender = new $class();
        $sender->send();
    }
}