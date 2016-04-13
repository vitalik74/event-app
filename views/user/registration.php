<?php
use app\models\form\RegistrationForm;
use yii\web\View;

/** @var $this View */
/* @var $model RegistrationForm */
?>
<?php $form = \yii\bootstrap\ActiveForm::begin([]); ?>

    <?= $form->field($model, 'username')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'password')->textInput() ?>

    <?= \yii\helpers\Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary']) ?>

<?php \yii\bootstrap\ActiveForm::end(); ?>