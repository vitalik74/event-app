<?php

namespace tests\codeception\unit\models;

use app\models\Article;
use app\tests\codeception\unit\fixtures\ArticleFixture;
use Codeception\Specify;
use yii\codeception\DbTestCase;

class ArticleTest extends DbTestCase
{
    use Specify;

    public function fixtures()
    {
        return [
            'events' => ArticleFixture::className(),
        ];
    }

    public function testValidateWrong()
    {
        $model = new Article();

        $this->specify('article wrond', function () use ($model) {
            expect('model should not validate', $model->validate())->false();

            expect('error message should be set', $model->errors)->hasKey('name');
            expect('error message should be set', $model->errors)->hasKey('text');
            expect('error message should be set', $model->errors)->hasKey('created_at');
            expect('error message should be set', $model->errors)->hasKey('updated_at');
            expect('error message should be set', $model->errors)->hasKey('user_id');
        });
    }

    public function testValidateCorrect()
    {
        $model = new Article([
            'name' => 'name',
            'text' => 'text',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'user_id' => null
        ]);

        $this->specify('event wrong', function () use ($model) {
            expect('model should not validate', $model->validate())->true();
        });
    }
}