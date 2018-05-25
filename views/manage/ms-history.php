<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel tecsin\pay2\models\searches\Pay2MsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pay2 Mobile/Server');
echo $this->render('/layouts/_menu');
?>
<div class="pay2-ms-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'msID',
          //  'aaaMerchantId',
            'mmmMemo:ntext',
            'tttTotalCost',
            'rrrMerchantRef',
            // 'cccRecurrentBillingStatus',
            // 'iiiRecurrenceInterval',
            // 'nnnNotificationUrl:url',
            // 'sssSuccessUrl:url',
            // 'fffFailUrl:url',
            // 'dddDeveloperCode',
            // 'cccCurrencyCode',
            // 'msResponse:ntext',
             'msExpireAt:datetime',
            // 'siteProductId',
             'msStatus',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{delete}'],        ],
    ]); ?>
<?php Pjax::end(); ?></div>
