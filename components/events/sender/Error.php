<?php

namespace components\events\sender;


use components\events\BaseEvent;
use Yii;

class Error extends BaseEvent implements SenderInterface
{
    /**
     * Send event to sms, browser or some else
     * @return mixed
     */
    public function send()
    {
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['fromEmail'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Шеф, все пропало!')
            ->setHtmlBody($this->error->getMessage())
            ->send();
    }
}