<?php
use app\models\BrowserEvent;
use yii\helpers\Html;
use yii\web\View;

/** @var $this View */
/* @var $model BrowserEvent */
?>
<div class="post">
    <h2><?= Html::encode($model->title) ?></h2>

    <?= $model->text ?> <br>
    <?= Yii::$app->formatter->asDatetime($model->created_at) ?> <br>
</div>