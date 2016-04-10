<?php
use yii\web\View;

/** @var $this View */

namespace app\components\events\sender;


interface SenderInterface
{
    /**
     * Send event to sms, browser or some else
     * @return mixed
     */
    public function send();
}