<?php

namespace app\components\events;


use app\components\events\sender\SenderFactory;
use ReflectionClass;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Object;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

class Event extends Object
{
    const TYPE_EVENT_EMAIL = 'email';
    const TYPE_EVENT_BROWSER = 'browser';

    const GROUP_EVENT_DEFAULT = 'defaultEvents';
    const GROUP_EVENT_CUSTOM = 'customEvents';

    /**
     * Namespaces where find models for bind
     * @var array
     */
    public $modelsNamespace = [];

    /**
     * Namespaces where find objects for run event
     * @var string
     */
    public $eventsNamespace = '';

    /**
     * Model event from AR
     * @var ActiveRecord
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
     * @var string
     */
    public $classNameKeySeparator = '||';

    /**
     * List of all events from models
     * @var array
     */
    private $_eventsFromModels = [];

    /**
     * Instance of event model
     * @var EventModelInterface|ActiveRecord
     */
    private $_modelEvent;

    /**
     * @var array
     */
    private $_modelsEvents;

    /**
     * @var array
     */
    private $_eventsWithRelatedModels = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->checkConfig(['modelEventClass', 'modelsNamespace', 'eventsNamespace']);

        $reflection = new ReflectionClass($this->modelEventClass);

        if (!$reflection->implementsInterface(__NAMESPACE__ . '\EventModelInterface')) {
            throw new InvalidConfigException('The "modelEventClass" property must be implements from "EventModelInterface"');
        }

        $this->findEvents();
        $this->_modelEvent = new $this->modelEventClass;
    }
    
    /**
     * @return array
     */
    public function getTypeEvents()
    {
        $reflection = new ReflectionClass(__CLASS__);

        return array_filter(array_flip($reflection->getConstants()), function ($data) {
            return strpos($data, 'TYPE_EVENT_') !== false ? true : false;
        });
    }

    /**
     * Events from models
     * @return array
     */
    public function getEventsFromModels()
    {
        return $this->_eventsFromModels;
    }
//+ дефолтные ивенты (из AR),
//+ ивенты той же модели но по условию (если status=0),
// ивенты которые завязаны на несколько моделей (модели просто с перечислением) + дефолтный ивент из AR,
// ивенты которые через анонимную функцию + дефолтный ивент из AR,
// отключение какие-от событий в классе
//
    /**
     * Bind event
     * @param Component $class
     * @param null|string $event Yii2 default event. Support array events
     * @param null|\Closure|array $data Data for passed to event
     */
    public function bind(Component $class, $event = null, $data = null)
    {
        if ($event == null) {
            $this->bindDefaultEvents($class);
        } else {
            if (is_array($event)) {
                foreach ($event as $value) {
                    $this->bind($class, $value, $data);
                }
            } else {
                $availableEvents = ArrayHelper::map($this->findEventModels(), $this->_modelEvent->getEventField(), function ($model) {
                    return $model;
                });
                $key = get_class($class) . $this->classNameKeySeparator . $event;

                if (ArrayHelper::isIn($key, array_keys($availableEvents))) {
                    $data = ArrayHelper::merge([
                        'type' => $availableEvents[$key]->{$this->_modelEvent->getTypeField()}
                    ], [
                        'data' => $data,
                        'sender' => $class,
                        'event' => $availableEvents[$key]
                    ]);
                    $class->on($event, [$this, 'create'], $data);
                }

                $this->bindEventsWithRelatedModels($class, $event, $data);
            }
        }
    }

    protected function bindEventsWithRelatedModels($class, $event, $data)
    {
        if (!($data instanceof \Closure) && isset($data['models']) && !empty($data['models'])) {
            $this->_eventsWithRelatedModels[get_class($class) . $this->classNameKeySeparator . $event] = $data['models'];
        }
    }

    /**
     * Unbind event
     * @param Component $class
     * @param string $event
     */
    public function unbind(Component $class, $event)
    {
        $class->off($event);
    }

    /**
     * @param \yii\base\Event $event
     * @throws InvalidConfigException
     */
    public function create(\yii\base\Event $event)
    {
        $data = $event->data;
        $sender = $data['sender'];
        $eventClass = $this->eventsNamespace . '\\' . ucfirst($data['type']);
        $event = $data['event'];

        if ($data['type'] !== null) {
            if ($data['data'] instanceof \Closure) {
                $data = $data['data']();
                //SenderFactory::create($sender, $event, $eventClass, $data);
            } elseif (!empty($data['data']['where']) && $this->checkCondition($sender, $data['data']['where'])) {
                $data = '';
                //SenderFactory::create($sender, $event, $eventClass);
            } else {
                $data = $data['data'];
                //SenderFactory::create($sender, $event, $eventClass, $data['data']);
            }

            SenderFactory::create($sender, $event, $eventClass, $data);
        }
    }

    /**
     * @param Component $class
     */
    public function bindDefaultEvents(Component $class)
    {
        $events = isset($this->getEventsFromModels()[get_class($class)][static::GROUP_EVENT_DEFAULT]) ? $this->getEventsFromModels()[get_class($class)][static::GROUP_EVENT_DEFAULT] : [];

        $events = ArrayHelper::getColumn($events, function ($event) use ($class) {
            return get_class($class) . $this->classNameKeySeparator . $event;
        });

        /** @var ActiveRecord|EventModelInterface $event */
        foreach ($this->findEventModels([$this->_modelEvent->getEventField() => array_keys($events)]) as $event) {
            $eventName = $this->getEventValue($event->{$event->getEventField()});

            if ($eventName !== null) {
                $this->bind($class, $eventName, $event->{$event->getTypeField()});
            }
        }
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
    public function getDefaultEvents($class)
    {
        $classes = ArrayHelper::merge(
            class_parents($class, true),
            class_implements($class, true)
        );

        return $this->events($classes, $class, $this->startDefaultEventName);
    }

    /**
     * @param $class
     * @return array
     */
    public function getCustomEvents($class)
    {
        return $this->events([$class], $class, $this->startCustomEventName);
    }

    /**
     * @param array $classes
     * @param $currentClass
     * @param $startEventName
     * @return array
     */
    protected function events(array $classes, $currentClass, $startEventName)
    {
        $events = [];

        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);

            foreach ($reflection->getConstants() as $constant => $value) {
                if (StringHelper::startsWith($constant, $startEventName) && $this->checkExecuteModels($class, $constant)) {
                    $events[$constant] = $currentClass . $this->classNameKeySeparator . $value;
                }
            }
        }

        return array_flip($events);
    }

    /**
     * If
     * @param string $class
     * @param string $constant
     * @return bool
     */
    protected function checkExecuteModels($class, $constant)
    {
        foreach ($this->executeModels as $key => $model) {
            if ((is_array($model) && $key == $class && ArrayHelper::isIn($constant, $model))
                || (is_string($model) && $class == $model)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Component $sender
     * @param array $where
     * @return bool
     */
    protected function checkCondition(Component $sender, array $where)
    {
        foreach ($where as $attribute => $value) {
            if ($sender->{$attribute} !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Events from DB
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function findEventModels($where = [])
    {
        $class = $this->modelEventClass;

        if (!empty($where)) {
            return $class::find()->where($where)->all();
        }

        if ($this->_modelsEvents == null) {
            $this->_modelsEvents = $class::find()->all();
        }

        return $this->_modelsEvents;
    }

    protected function checkConfig(array $properties)
    {
        foreach ($properties as $property) {
            if ($this->$property === null) {
                throw new InvalidConfigException('The "' . $property . '" property must be set.');
            }
        }
    }

    /**
     * Explode event name
     * @param $eventName
     * @return null|string
     */
    protected function getEventValue($eventName)
    {
        $tmp = $this->explode($eventName);

        return isset($tmp[1]) ? $tmp[1] : null;
    }

    /**
     * Explode event class
     * @param $eventName
     * @return null
     */
    protected function getEventClassValue($eventName)
    {
        $tmp = $this->explode($eventName);

        return isset($tmp[0]) ? $tmp[0] : null;
    }

    /**
     * @param $eventName
     * @return array
     */
    protected function explode($eventName)
    {
        return explode($this->classNameKeySeparator, $eventName);
    }

    /**
     * @param $event
     * @return array
     */
    public function getFields($event)
    {
        $class = $this->getEventClassValue($event);

        if ($class == null) {
            return [];
        }

        /** @var Model $model */
        $model = new $class();
        $class = $this->replaceNamespace($class);
        $fields = [];

        if (!($model instanceof Model)) {
            return [];
        }

        $this->getFieldsByAttributes($fields, $class, $model);

        // check in related model
        if (ArrayHelper::isIn($event, array_keys($this->_eventsWithRelatedModels))) {
            foreach ($this->_eventsWithRelatedModels[$event] as $relatedModel) {
                $method = 'get' . ucfirst($relatedModel);
                $query = $model->{$method}();

                if ($query instanceof ActiveQuery) {
                    $model = new $query->modelClass();
                    $this->getFieldsByAttributes($fields, $this->replaceNamespace($query->modelClass), $model);
                }
            }
        }

        return array_unique($fields);
    }

    /**
     * @param $class
     * @return mixed
     */
    protected function replaceNamespace($class)
    {
        return str_replace($this->modelsNamespace, '', $class);
    }

    /**
     * @param $fields
     * @param $class
     * @param Model $model
     * @return array
     */
    protected function getFieldsByAttributes(&$fields, $class, Model $model)
    {
        $attributes = $model->getAttributes();

        if (!empty($attributes)) {
            foreach (array_keys($attributes) as $attribute) {
                $fields[] = $class . $this->classNameKeySeparator . $attribute;
            }
        }
    }
}