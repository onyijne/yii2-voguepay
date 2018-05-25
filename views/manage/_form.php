<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tecsin\pay2\models\Pay2Setup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay2-setup-form">

    <?php $form = ActiveForm::begin(); 

     echo $form->field($model, 'merchant_id')->textInput(['maxlength' => true]);
     
     if(Yii::$app->user->can('admin')){
         echo $form->field($model, 'success_url')->textInput(['type' => 'url']).
            $form->field($model, 'failure_url')->textInput(['type' => 'url']);     
     }
    
      ?>

    <?= $form->field($model, 'api_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'voguepay_email')->textInput(['type' => 'email']) ?>
    <hr>
    <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'account_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'account_number')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'account_type')->dropDownList(
            ['savings' => 'Savings', 'current' => 'Current', 'credit' => 'Credit', 'prepaid' => 'Prepaid', 'unsure' => 'Not Sure'], 
            ['prompt'=>'Select One...', 'selected' => $model->account_type]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
