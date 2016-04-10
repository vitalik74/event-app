<?php

namespace app\components\events;


use components\events\sender\Error;
use yii\base\Event;
use yii\base\Exception;

class BaseEvent extends Event
{
    /**
     * Error handler
     * @var \Exception
     */
    protected $error;


    /**
     * Get event from DB
     * @param $type
     * @throws Exception
     */
    public function getEvent($type)
    {
        $event = Event::findOne(['type' => $type]);

        if ($event == null) {
            throw new Exception('Not event in DB, event type is "' . $type . '"');
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        self::on(EventFactory::className(), EventFactory::EVENT_ERROR, '', $this->data);
    }

    /**
     * Send error
     * @param \Exception $e
     */
    public function sendError(\Exception $e)
    {
        $this->error = $e;
        self::trigger(EventFactory::className(), EventFactory::EVENT_ERROR, $this);
    }
}