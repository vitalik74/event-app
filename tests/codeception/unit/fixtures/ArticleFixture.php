<?php

namespace app\tests\codeception\unit\fixtures;


use yii\test\ActiveFixture;

class ArticleFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Article';
}