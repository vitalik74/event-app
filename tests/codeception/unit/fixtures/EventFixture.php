<?php

namespace app\tests\codeception\unit\fixtures;


use Codeception\Specify;
use yii\test\ActiveFixture;

class EventFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Event';
}