<?php

namespace app\events;


use app\components\events\sender\BaseSender;
use app\components\events\sender\SenderInterface;
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
            $title = $this->replaceTitle('id статьи {articleId}


короткий текст статьи {articleShortText}');

            Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['fromEmail'])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject($this->getTitle())
                ->setHtmlBody($this->getText())
                ->send();
        } catch (\Exception $e) {
            $this->sendError($e);
        }
    }
}