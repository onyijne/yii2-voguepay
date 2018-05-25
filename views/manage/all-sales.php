<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel tecsin\pay2\models\searches\Pay2SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pay2 Sales');
//$this->params['breadcrumbs'][] = $this->title;
echo $this->render('/layouts/_menu');
?>
<div class="pay2-sales-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'remark',
            'received_amount',
            'mature_date:date',
            'transaction_date:date',
            // 'memo:ntext',
             'total',
            // 'total_paid',
            // 'extra_charges',
            // 'gateway',
            // 'user_id',
            // 'referrer',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
