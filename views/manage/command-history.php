<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel tecsin\pay2\models\searches\Pay2CommandHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pay2 Command Histories');
//$this->params['breadcrumbs'][] = $this->title;
echo $this->render('/layouts/_menu');
?>
<div class="pay2-command-history-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
           // 'ref',
            'task',
            'type',
            'status',

        ],
    ]); ?>
<?php Pjax::end(); ?></div>
