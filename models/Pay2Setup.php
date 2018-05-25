<?php

namespace tecsin\pay2\models;

use Yii;

/**
 * This is the model class for table "{{%pay2_setup}}".
 *
 * @property integer $id
 * @property string $merchant_id
 * @property string $success_url
 * @property string $failure_url
 * @property string $api_key
 * @property string $voguepay_email
 * @property string $bank_name
 * @property string $account_name
 * @property string $account_number
 * @property string $account_type
 * @property string $payment_instruction
 */
class Pay2Setup extends \yii\db\ActiveRecord
{
    public $developer_code;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pay2_setup}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['merchant_id', 'success_url', 'failure_url', 'api_key', 'voguepay_email'], 'required'],
            [['merchant_id', 'success_url', 'failure_url', 'api_key'], 'string', 'max' => 200],
            ['voguepay_email', 'email'],
            [['bank_name', 'account_name', 'account_number', 'account_type'], 'string', 'max' => 50],
            [['payment_instruction'], 'string', 'default', 'value' => 'Pay online via VoguePay or pay via bank transfer and send your payment details to support']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('pay2', 'ID'),
            'merchant_id' => Yii::t('pay2', 'Merchant ID'),
            'success_url' => Yii::t('pay2', 'Success Url'),
            'failure_url' => Yii::t('pay2', 'Failure Url'),
            'api_key' => Yii::t('pay2', 'Api Key'),
            'voguepay_email' => Yii::t('pay2', 'Voguepay Email'),
            'bank_name' => Yii::t('pay2', 'Bank Name'),
            'account_name' => Yii::t('pay2', 'Account Name'),
            'account_number' => Yii::t('pay2', 'Account Number'),
            'account_type' => Yii::t('pay2', 'Account Type'),
            'payment_instruction' => Yii::t('pay2', 'Payment Instruction'),
        ];
    }
    
    public function getDevCode()
    {
        return $this->developer_code = '573cedec3bee0';
    }
    
    /**
     * 
     * @return \tecsin\pay2\models\Pay2Setup
     */
    public static function getModel()
    {
        $model = static::findOne(1);
        if(!$model){
            throw new \yii\base\InvalidConfigException('Pay2 Setup table is missing or wrong table id called.');
        }
        return $model;
    }
}
