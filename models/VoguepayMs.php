<?php

namespace tecsin\pay2\models;

use Yii;
use tecsin\pay2\models\queries\VoguepayMsQuery;
use yii\validators\UrlValidator;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use yii\web\Response;
use yii\httpclient\Client;


/**
 * This is the model class for table "{{%voguepay_ms}}".
 *
 * @property integer $msID
 * @property string $aaaMerchantId
 * @property string $mmmMemo
 * @property string $tttTotalCost
 * @property string $rrrMerchantRef
 * @property integer $cccRecurrentBillingStatus
 * @property integer $iiiRecurrenceInterval
 * @property string $nnnNotificationUrl
 * @property string $sssSuccessUrl
 * @property string $fffFailUrl
 * @property string $dddDeveloperCode
 * @property string $cccCurrencyCode
 * @property string $msResponse
 * @property string $msExpireAt
 * @property string $siteProductId
 * @property string $msStatus
 * 
 */
class VoguepayMs extends \yii\db\ActiveRecord
{
    /**
     *
     * @var bool If the response should be returned as a button, defaults to false, which means redirect to the rsponse link.
     */
    public $showButton = false;
    
    /**
     *
     * @var string $requestLink The link to send to VoguePay for a response link on success or failure error code.
     */
    public $requestLink;
       
    public function __construct($config = array()) {
        $this->dddDeveloperCode = '573cedec3bee0';
        if(!$this->isNewRecord){
            //set $requestLink is not new record
            $this->setRequest();
        }
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%voguepay_ms}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aaaMerchantId', 'mmmMemo', 'tttTotalCost'], 'required'],
            [['mmmMemo', 'msResponse'], 'string'],
            [['cccRecurrentBillingStatus', 'iiiRecurrenceInterval'], 'integer'],
            [['aaaMerchantId', 'tttTotalCost', 'rrrMerchantRef', 'nnnNotificationUrl', 'sssSuccessUrl', 'fffFailUrl', 'dddDeveloperCode', 'cccCurrencyCode', 'msExpireAt', 'siteProductId', 'msStatus'], 'string', 'max' => 255],
            [['rrrMerchantRef'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msID' => Yii::t('app', 'Ms ID'),
            'aaaMerchantId' => Yii::t('app', 'Merchant ID'),
            'mmmMemo' => Yii::t('app', 'Memo'),
            'tttTotalCost' => Yii::t('app', 'Total Cost'),
            'rrrMerchantRef' => Yii::t('app', 'Transaction Reference'),
            'cccRecurrentBillingStatus' => Yii::t('app', 'Recurrent Billing Status'),
            'iiiRecurrenceInterval' => Yii::t('app', 'No of days between each recurrent billing if recurrent is set to true.'),
            'nnnNotificationUrl' => Yii::t('app', 'Notification Url'),
            'sssSuccessUrl' => Yii::t('app', 'Success Url'),
            'fffFailUrl' => Yii::t('app', 'Fail Url'),
            'dddDeveloperCode' => Yii::t('app', 'Developer Code'),
            'cccCurrencyCode' => Yii::t('app', 'Currency Code'),
            'msResponse' => Yii::t('app', 'Response Link'),
            'msCreatedAt' => Yii::t('app', 'Ms Created At'),
            'msUpdatedAt' => Yii::t('app', 'Ms Updated At'),
            'siteProductId' => Yii::t('app', 'Site Product ID'),
            'msStatus' => Yii::t('app', 'Ms Status'),
        ];
    }

    /**
     * @inheritdoc
     * @return VoguepayMsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VoguepayMsQuery(get_called_class());
    }
    
    /**
     * Sends request to voguepay for a payment link and saves if successful
     * 
     * @return \tecsin\pay2\models\VoguepayMs
     */
    public function sendRequest()
    {
        //to avoid sending request twice when response has not expired
        if(self::findOne(['msID' => $this->msID])){
            if(!$this->hasExpired()){
                return $this;
            }
        }
        // Send the request & save response to $msResponse
        $client = new Client([
            'baseUrl' => 'https://www.voguepay.com',
            ]);
        $response = $client->get($this->requestLink)->send();
        $this->msResponse = $response->content;
        if((new UrlValidator())->validate($this->msResponse)){
            $this->msExpireAt = strtotime("next 24 hours");
            $this->save();
        }
    }
    
    /**
     * 
     * @return string Sets the request parameters to be sent to voguepay
     * @throws InvalidConfigException
     */
    public function setRequest()
    {
        if(empty($this->aaaMerchantId || $this->mmmMemo || $this->tttTotalCost)){
            throw new InvalidConfigException($this->aaaMerchantId. ' '.$this->mmmMemo.' and '.$this->tttTotalCost.' are required.');
        }
        $this->requestLink = "?p=linkToken&v_merchant_id=$this->aaaMerchantId&memo=$this->mmmMemo&total=$this->tttTotalCost";
        if(!empty($this->rrrMerchantRef)){
            $this->requestLink .= "&merchant_ref=$this->rrrMerchantRef";
        }
        if($this->cccRecurrentBillingStatus){
            if($this->iiiRecurrenceInterval <= 1){
                throw new InvalidConfigException( 'iiiRecurrenceInterval most be greater than 1 if cccRecurrentBillingStatus is set to true');
            }
            $this->requestLink .= "&recurrent=$this->cccRecurrentBillingStatus&interval=$this->iiiRecurrenceInterval";
        }
        if(!empty($this->nnnNotificationUrl)){
            $this->requestLink .= "&notify_url=$this->nnnNotificationUrl";
        }
        if(!empty($this->sssSuccessUrl)){
            $this->requestLink .= "&success_url=$this->sssSuccessUrl";
        }
        if(!empty($this->fffFailUrl)){
            $this->requestLink .= "&fail_url=$this->fffFailUrl";
        }
        $this->requestLink .= "&developer_code=$this->dddDeveloperCode";
        return $this->requestLink;
    }
    
    /**
     * 
     * First checks if $msResponse is a valid url, then redirects or send json formated response depending on $showButton value.
     * @return mixed Redirects to payment page or send result in json
     */
    public function sendResponse()
    {
        $validator = new UrlValidator();
        if(!$validator->validate($this->msResponse)){
            //not a valid url, you will be getting a json response error message to work with.
            Yii::$app->response->format =   Response::FORMAT_JSON;
            return Json::encode([
                'status' => 'error',
                'error' => [
                    'message' => $this->getResponseError($this->msResponse)
                ]
            ]);
        }
        //not showing a button to the user, redirect to payment pay automatically.
        if(!$this->showButton){
            return Yii::$app->getResponse()->redirect($this->msResponse);
        } else {
            //show a pay button to the user, you will be getting a json response payment link to work with.    
            return Json::encode([
                'status' => 'success',
                'success' => [
                    'message' => $this->msResponse
                ]
            ]);
        }
    }
    
    /**
     * 
     * @param string $ref The reference coe to search database with or the current ref if not set
     * @return string
     */
    public function getResponse($ref = null)
    {
        //no transaction reference code was provided
        if($ref == null){
            return $this->msResponse;
        }
        //use the transaction reference code provided to fetch the response link
        $r = self::findOne(['rrrMerchantRef' => $ref]);
        return $r->msResponse;
    }
    
    /**
     * 
     * @param bool $value
     */
    public function setRecurrentBillingStatus(bool $value)
    {
        $this->cccRecurrentBillingStatus = $value;
    }
    
    public function getRecurrentBillingStatus()
    {
        return $this->cccCurrencyCode;
    }
    
    /**
     * 
     * @param string $url
     */
    public function setSuccessUrl(string $url)
    {
        $this->sssSuccessUrl = $url;
    }
    
    public function getSuccessUrl()
    {
        return $this->sssSuccessUrl;
    }
    
    /**
     * 
     * @param string $url
     */
    public function setFailUrl(string $url)
    {
        $this->fffFailUrl = $url;
    }
    
    public function getFailUrl()
    {
        return $this->fffFailUrl;
    }
    
    /**
     * 
     * @param string $url
     */
    public function setNotificationUrl(string $url)
    {
        $this->nnnNotificationUrl = $url;
    }
    
    public function getNotificationUrl()
    {
        return $this->nnnNotificationUrl;
    }
    
    /**
     * 
     * @param string $currencyCode
     */
    public function setCurrencyCode(string $currencyCode)
    {
        $this->cccCurrencyCode = $currencyCode;
    }
    
    public function getCurrencyCode()
    {
        return $this->cccCurrencyCode;
    }
    
    /**
     * 
     * Expires within 23hours, 50minutes so as to be 10 minutes lower than the original 
     * 24hours from VoguePay to ensure consistency.
     * @return boolean If the response link ($msResponse) has expired.
     */
    public function hasExpired()
    {
        if($this->msExpireAt >= strtotime('10 minutes ago')){
            return true;
        }
        return false;
    }
    
    public function getResponseError($errorCode)
    {
        $messages = [
            '-1' => 'Unable to process command',
            '-3' => ' Empty Merchant ID',
            '-4' => 'Memo is empty',
            '-14' => 'Invalid Merchant ID',
            '-100' => 'No result'
        ];
        if(key_exists($errorCode, $messages)){
            return $messages[$errorCode];
        } else {
            return 'Unknown error occured, please try again or contact an admin.';    
        }
    }
}
