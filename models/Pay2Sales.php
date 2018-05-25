<?php

namespace tecsin\pay2\models;

use Yii;

/**
 * This is the model class for table "{{%pay2_sales}}".
 *
 * @property integer $id
 * @property string $ref add Yii::$app->user->id.'_user' as prefix 
 * @property string $remark
 * @property string $received_amount
 * @property string $mature_date
 * @property string $transaction_date
 * @property string $memo
 * @property string $total
 * @property string $total_paid
 * @property string $extra_charges
 * @property string $gateway
 * @property string $user_id 
 * @property string $referrer
 */
class Pay2Sales extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pay2_sales}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memo'], 'string'],
            [['ref', 'remark', 'received_amount', 'mature_date', 'transaction_date', 'total', 'total_paid', 'extra_charges', 'gateway', 'user_id', 'referrer'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('pay2', 'ID'),
            'ref' => Yii::t('pay2', 'Transaction Reference'),
            'remark' => Yii::t('pay2', 'Transction Remark'),
            'received_amount' => Yii::t('pay2', 'Amount Credited To Merchant'),
            'mature_date' => Yii::t('pay2', 'Fund Mature Date'),
            'transaction_date' => Yii::t('pay2', 'Transaction Date'),
            'memo' => Yii::t('pay2', 'Memo'),
            'total' => Yii::t('pay2', 'Total Cost'),
            'total_paid' => Yii::t('pay2', 'Total Paid'),
            'extra_charges' => Yii::t('pay2', 'Extra Charges'),
            'gateway' => Yii::t('pay2', 'Gateway'),
            'user_id' => Yii::t('pay2', 'User ID'),
            'referrer' => Yii::t('pay2', 'Referrer'),
        ];
    }

    /**
     * @inheritdoc
     * @return \tecsin\pay2\models\queries\Pay2SalesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \tecsin\pay2\models\queries\Pay2SalesQuery(get_called_class());
    }
}
