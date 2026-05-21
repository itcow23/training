<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Order $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account_id')->textInput() ?>

    <?= $form->field($model, 'membership_level_id')->textInput() ?>

    <?= $form->field($model, 'shipping_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'discount')->textInput() ?>

    <?= $form->field($model, 'subtotal')->textInput() ?>

    <?= $form->field($model, 'shipping_fee')->textInput() ?>

    <?= $form->field($model, 'final_total')->textInput() ?>

    <?= $form->field($model, 'pay_method')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
