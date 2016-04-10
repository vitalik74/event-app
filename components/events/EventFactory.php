<?php

namespace app\components\events;


use ReflectionClass;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Object;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

class EventFactory extends Object
{
    const TYPE_EVENT_EMAIL = 'email';
    const TYPE_EVENT_BROWSER = 'browser';
    const TYPE_EVENT_SMS = 'sms';
    const TYPE_EVENT_GOD = 'God';
    const TYPE_EVENT_ERROR = 'error';

    const GROUP_EVENT_DEFAULT = 'defaultEvents';
    const GROUP_EVENT_CUSTOM = 'customEvents';

    /**
     * Namespaces where find models for bind
     * @var array
     */
    public $modelsNamespace = [];

    /**
     * Model event from AR
     * @var string
     */
    public $modelEventClass;

    /**
     *
     * @var array
     */
    public $executeModels = [];

    /**
     * Find models in namespace path in sub dir
     * @var bool
     */
    public $findModelsRecursive = false;

    /**
     * @var string
     */
    public $modelFileExtension = '.php';

    /**
     * @var string
     */
    public $startDefaultEventName = 'EVENT_';

    /**
     * @var string
     */
    public $startCustomEventName = 'EVENT_CUSTOM';

    /**
     * List of all events from models
     * @var array
     */
    private $_eventsFromModels = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->checkConfig(['modelEventClass', 'modelsNamespace']);

        $this->findEvents();
    }
    
    /**
     * @return array
     */
    public function getTypeEvents()
    {
        $reflection = new ReflectionClass(__CLASS__);

        return array_flip($reflection->getConstants());
    }

    /**
     * @return array
     */
    public function getEventsFromModels()
    {
        return $this->_eventsFromModels;
    }
// дефолтные ивенты (из AR),
// ивенты той же модели но по условию (если status=0),
// ивенты которые завязаны на несколько моделей (модели просто с перечислением) + дефолтный ивент из AR,
// ивенты которые через анонимную функцию + дефолтный ивент из AR,
// отключение какие-от событий в классе
//
    /**
     * Bind event
     * @param Component $class
     * @param string|\Closure|array $event Yii2 default event. Support array events
     */
    public function bind(Component $class, $event)
    {
        $class->on($event, [$this, 'create']);
    }

    /**
     * @param Component $class
     */
    public function bindDefaultEvents(Component $class)
    {
        foreach ($this->getEventsFromModels()[static::GROUP_EVENT_DEFAULT] as $event) {
            $this->bind($class, $event);
        }
    }

    /**
     * Unbind event
     * @param Component $class
     * @param bool|true $defaultEvents
     */
    public function unbind(Component $class, $defaultEvents = true)
    {

    }

    protected function findEvents()
    {
        foreach ($this->modelsNamespace as $namespace) {
            $fileModels =  FileHelper::findFiles(Yii::getAlias(FileHelper::normalizePath('@' . $namespace, '/')), ['recursive' => $this->findModelsRecursive]);

            foreach ($fileModels as $file) {
                $class = $namespace . '\\' . basename($file, $this->modelFileExtension);

                $this->_eventsFromModels[$class][static::GROUP_EVENT_CUSTOM] = $this->getCustomEvents($class);
                $this->_eventsFromModels[$class][static::GROUP_EVENT_DEFAULT] = $this->getDefaultEvents($class);
            }
        }
    }

    /**
     * Find Yii2 events in parent, implements class
     * @param $class
     * @return array
     */
    protected function getDefaultEvents($class)
    {
        $classes = ArrayHelper::merge(
            class_parents($class, true),
            class_implements($class, true)
        );

        return $this->events($classes, $this->startDefaultEventName);
    }

    /**
     * @param $class
     * @return array
     */
    protected function getCustomEvents($class)
    {
        return $this->events([$class], $this->startCustomEventName);
    }

    /**
     * @param array $classes
     * @param $startEventName
     * @return array
     */
    protected function events(array $classes, $startEventName)
    {
        $events = [];

        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);

            foreach ($reflection->getConstants() as $constant => $value) {
                if (StringHelper::startsWith($constant, $startEventName)) {
                    $events[$constant] = $value;
                }
            }
        }

        return array_flip($events);
    }

    public function create()
    {
        
    }

    protected function findEventModels()
    {
        
    }

    protected function checkConfig(array $properties)
    {
        foreach ($properties as $property) {
            if ($this->$property === null) {
                throw new InvalidConfigException('The "' . $property . '" property must be set.');
            }
        }
    }
}