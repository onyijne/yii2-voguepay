<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model tecsin\pay2\models\Pay2Setup */

$this->title = Yii::t('app', 'Setup Pay2');
echo $this->render('/layouts/_menu');
?>
<div class="pay2-ms-create" style="width: 98%; margin: 0 auto; max-width: 320px;">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
