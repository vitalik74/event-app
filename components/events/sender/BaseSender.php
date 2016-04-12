<?php

namespace app\components\events\sender;


use app\components\events\Event;
use app\components\events\EventModelInterface;

use yii\base\Component;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class BaseSender extends Component
{
    const EVENT_ERROR = 'error';

    /**
     * Data to passed
     * @var
     */
    public $data;

    /**
     * Model from calling event
     * @var
     */
    public $sender;

    /**
     * Event from DB
     * @var EventModelInterface|ActiveRecord
     */
    public $eventModel;

    /**
     * Name sender
     * @var
     */
    public $name;

    /**
     * Error handler
     * @var \Exception
     */
    protected $error;

    /**
     * @inheritdoc
     */


    /**
     * Send error
     * @param \Exception $e
     */
    public function sendError(\Exception $e)
    {
        $this->error = $e;
        // send error to email or some else
    }

    /**
     * @param $text
     * @return string
     */
    public function replaceText($text)
    {
        return $this->replace($text);
    }

    /**
     * @param $title
     * @return string
     */
    public function replaceTitle($title)
    {
        return $this->replace($title);
    }

    /**
     * @param $str
     * @return mixed
     */
    protected function replace($str)
    {
        $variables = $this->getVariableFromText($str);

        return str_replace($variables, $this->getValueFromModel($variables),$str);
    }

    /**
     * @param $str
     * @return mixed
     */
    protected function getVariableFromText($str)
    {
        preg_match_all('/{(.*?)}/is', $str, $match);

        return $match[0];
    }

    /**
     * @param array $variables
     */
    protected function getValueFromModel(array $variables)
    {

    }
}