<?php

namespace app\events;


use app\components\events\sender\BaseSender;
use app\components\events\sender\SenderInterface;
use app\models\User;
use Yii;

class Email extends BaseSender implements SenderInterface
{
    /**
     * Send event to sms, browser or some else
     * @return mixed
     */
    public function send()
    {
        try {
            /** @var User $user */
            $user = $this->user;

            Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['fromEmail'])
                ->setTo($user->email)
                ->setSubject($this->getTitle())
                ->setHtmlBody($this->getText())
                ->send();
        } catch (\Exception $e) {
            $this->sendError($e);
        }
    }
}