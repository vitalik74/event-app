<?php

namespace app\components\events\sender;


use app\components\events\Event;
use app\components\events\EventModelInterface;

use Yii;
use yii\base\Component;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\IdentityInterface;

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
     * @var Model
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
     * User model
     * @var ActiveRecord|IdentityInterface
     */
    public $user;


    /**
     * Send error
     * @param \Exception $e
     */
    public function sendError(\Exception $e)
    {
        // send error to email or some else
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['fromEmail'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Шеф, все пропало!')
            ->setHtmlBody($e->getMessage())
            ->send();
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
     * @return array
     */
    protected function getVariableFromText($str)
    {
        preg_match_all('/{(.*?)}/is', $str, $match);

        return isset($match[0]) ? $match[0] : [];
    }

    /**
     * @param array $variables
     * @return array
     */
    protected function getValueFromModel(array $variables)
    {
        $values = $res = [];

        if ($this->data instanceof \Closure) {
            $closure = $this->data;
            $valuesFromClosure = $closure();
            $this->getAttributesFromClosure($values, $valuesFromClosure);
        } elseif (!empty($this->data['models'])) {
            foreach ($this->data['models'] as $model) {
                $relatedModel = $this->sender->{$model};

                if ($relatedModel !== null) {
                    $this->getAttributes($values, $relatedModel);
                }
            }
        }

        // bind attributes from current model
        $this->getAttributes($values, $this->sender);

        foreach ($variables as $variable) {
            if (is_array($values) && isset($values[$variable])) {
                $res[] = $values[$variable];
            }
        }

        return $res;
    }

    /**
     * @param $values
     * @param Model $model
     */
    protected function getAttributes(&$values, Model $model)
    {
        foreach ($model->getAttributes() as $attribute => $value) {
            $className = explode('\\', get_class($model));
            $className = $className[count($className) - 1];

            $values[$this->getKeyFromAttribute($className, $attribute)] = $value;
        }
    }

    /**
     * @param $values
     * @param $valuesFromClosure
     */
    protected function getAttributesFromClosure(&$values, $valuesFromClosure)
    {
        foreach ($valuesFromClosure as $attribute => $value) {
            $values[$this->getKeyFromAttribute('Closure', $attribute)] = $value;
        }

    }

    /**
     * @param $className
     * @param $attribute
     * @return string
     */
    protected function getKeyFromAttribute($className, $attribute)
    {
        return '{' . lcfirst(Inflector::camelize($className . '_'. $attribute)) . '}';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->replaceTitle($this->eventModel->{$this->eventModel->getTitleField()});
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->replaceTitle($this->eventModel->{$this->eventModel->getTextField()});
    }
}