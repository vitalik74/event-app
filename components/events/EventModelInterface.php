<?php
use yii\web\View;

/** @var $this View */

namespace app\components\events;


interface EventModelInterface
{
    /**
     * @return string
     */
    public function getTypeField();

    /**
     * @return string
     */
    public function getEventField();

    /**
     * @return string
     */
    public function getUserIdField();
}