<?php

namespace app\components\events\sender;


use app\components\events\BaseEvent;
use Yii;

class Email extends BaseEvent implements SenderInterface
{

    /**
     * Send event to sms, browser or some else
     * @return mixed
     */
    public function send()
    {
        try {
            $eventModel = $this->getEvent($this->name);

            Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['fromEmail'])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject('Шеф, все пропало!')
                ->setHtmlBody($this->config['msgText'])
                ->send();
        } catch (\Exception $e) {
            $this->sendError($e);
        }
    }
}