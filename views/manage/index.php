<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model tecsin\pay2\models\Pay2Setup */

$this->title = 'Pay2 (VoguePay) Setup';
echo $this->render('/layouts/_menu');
?>
<div class="pay2-setup-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), [ 'update'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          //  'id',
            'merchant_id',
            'success_url:url',
            'failure_url:url',
            'api_key',
            'voguepay_email:email',
            'bank_name',
            'account_name',
            'account_number',
            'account_type'
        ],
    ]) ?>

</div>
