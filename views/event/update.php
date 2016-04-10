<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $users app\models\User[] */
/* @var $typeEvents [] */
/* @var $events [] */

$this->title = 'Update Event: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'typeEvents' => $typeEvents,
        'events' => $events
    ]) ?>

</div>
