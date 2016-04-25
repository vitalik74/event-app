<?php

use app\components\events\Event;
use app\models\EventField;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $users app\models\User[] */
/* @var $events [] */
/* @var $typeEvents [] */
/* @var $form yii\widgets\ActiveForm */
/* @var $defaultEvent [] */
/* @var $modelTypeEventFields EventField */

$js = <<<JS
    $('#event-event').on('change', function () {
        var defaultEvent = $('.default-event'),
            selected = $(':selected', this),
            val = selected.closest('optgroup').attr('label');

        if (val == 'customEvents') {
            defaultEvent.show();
        } else {
            defaultEvent.hide();
        }

        $.get('/event/get-fields?event=' + $(this).val(), function (data) {
            $('.alert-event').html(data).show();
        });
    });
JS;

$this->registerJs($js);
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'event')->dropDownList($events, ['prompt' => 'Выберите']) ?>

    <?= $form->field($model, 'default_event', ['options' => ['class' => 'default-event', 'style' => !empty($model->default_event) ? 'display:block;' : '']])->dropDownList($defaultEvent, ['multiple' => 'multiple', 'size' => '10']) ?>

    <?= $form->field($model, 'user_id')->dropDownList($users, ['prompt' => 'Всем']) ?>

    <div class="alert alert-success alert-event">
    </div>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'type')->dropDownList($typeEvents, ['multiple' => 'multiple', 'size' => '3']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
