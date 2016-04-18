<?php
use yii\helpers\Inflector;
use yii\web\View;

/** @var $this View */
/* @var $key string */
?>
<?= lcfirst(Inflector::camelize($model)) ?>