<?php

namespace app\events;

use app\components\events\sender\BaseSender;
use app\components\events\sender\SenderInterface;
use app\models\BrowserEvent;

class Browser extends BaseSender implements SenderInterface
{

    /**
     * Send event to sms, browser or some else
     * @return mixed
     */
    public function send()
    {
        try {
            $browser = new BrowserEvent([
                'title' => $this->getTitle(),
                'text' => $this->getText(),
                'from_user_id' => 1
            ]);
            $browser->save();
        } catch (\Exception $e) {
            $this->sendError($e);
        }
    }
}