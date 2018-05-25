<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model tecsin\pay2\models\Pay2Sales */

$this->title = $model->ref;
echo $this->render('/layouts/_menu');
?>
<div class="pay2-sales-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ref',
            'remark',
            'received_amount',
            'mature_date',
            'transaction_date',
            'memo:ntext',
            'total',
            'total_paid',
            'extra_charges',
            'gateway',
            [
                'attribute' => 'user_id',
                'value' => function($model){
                   $module = Yii::$app->getModule('pay2');
                   $class = ($module) ? Yii::createObject($module->userModelClass) : Yii::$app->user->identityClass;
                   $user =  $class::findIdentity($model->user_id);
                   return ($user) ? $user->username : 'Deleted user';
                }
            ],
            'referrer',
        ],
    ]) ?>

</div>
