<?php
use app\models\BrowserEvent;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/** @var $this View */
/* @var $model BrowserEvent */

$js = <<<JS
    $('.btn-read').on('click', function () {
        var _this = this;
        $.ajax({
            type: 'post',
            url: '/browser-event/set-read?id=' + $(this).attr('id'),
            success: function (response) {
                if (response.success) {
                    $(_this).parent().addClass('alert-info');
                    $(_this).hide();
                }
            }
        });
    });
JS;

$this->registerJs($js);
?>
<div class="alert <?= empty($model->viewed) ? 'alert-success' : 'alert-info' ?>">
    <strong><?= Html::encode($model->title) ?></strong><br>

    <?= $model->text ?> <br>
    <?= Yii::$app->formatter->asDate($model->created_at) ?> <br>

    <?php if (empty($model->viewed)): ?>
        <?= Html::button('Read', ['class' => 'btn btn-success btn-read', 'id' => $model->id]) ?>
    <?php endif ?>
</div>