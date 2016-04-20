<?php
use yii\data\ArrayDataProvider;
use yii\web\View;
use yii\widgets\ListView;

/** @var $this View */
/* @var $dataProvider ArrayDataProvider */
?>
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_post',
]); ?>