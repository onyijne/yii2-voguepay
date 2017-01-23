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
    const EVENT_BEFORE_SET_REQUEST = 'beforeSetRequest';
    const EVENT_AFTER_SET_REQUEST = 'afterSetRequest';
    const EVENT_BEFORE_SEND_REQUEST = 'beforeSendRequest';
    const EVENT_AFTER_SEND_REQUEST = 'afterSendRequest';
    const EVENT_BEFORE_SEND_RESPONSE = 'beforeSendResponse';
    const EVENT_AFTER_SEND_RESPONSE = 'afterSendResponse';

    /**
     * If the response should be returned as a pay button, defaults to false, 
     * which means redirect to the response link (voguepay) for payment.
     *
     * @var bool 
     */
    public $showPayButton = false;
    
    /**
     * The parameters (without https://www.voguepay.com) to send to VoguePay for a response link on success 
     * or failure error code if not successful.
     *
     * @var string
     */
    public $requestParams;
    
     /**
     * Constructor.
     * The default implementation does three things:
     *
     * - Checks if isNewRecord, if false, calls the setRequest method to set requestParams property.
     * - Initializes the object with the given configuration `$config`.
     * - Call [[init()]].
     *
     * If this method is overridden in a child class, it is recommended that
     *
     * - the last parameter of the constructor is a configuration array, like `$config` here.
     * - call the parent implementation at the end of the constructor.
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = array()) {
        if(!$this->isNewRecord){
            //set $requestParams as it is not new a record
            $this->setRequest();
        }
        parent::__construct($config);
    }
    
    public function init() {
        parent::init();
        $this->on('beforeSetRequest', [$this, 'beforeSetRequest']);
        $this->on('afterSetRequest', [$this, 'afterSetRequest']);
        $this->on('beforeSendRequest', [$this, 'beforeSendRequest']);
        $this->on('afterSendRequest', [$this, 'afterSendRequest']);
        $this->on('beforeSendResponse', [$this, 'beforeSendResponse']);
        $this->on('beforeSendResponse', [$this, 'beforeSendResponse']);
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
     * Set the parameters to send with this request to VoguePay
     * 
     * @throws InvalidConfigException If either aaaMerchantId, mmmMemo, or tttTotalCost is empty.
     */
    public function setRequest()
    {
        if(empty($this->aaaMerchantId || $this->mmmMemo || $this->tttTotalCost)){
            throw new InvalidConfigException($this->aaaMerchantId. ' '.$this->mmmMemo.' and '.$this->tttTotalCost.' are required.');
        }
        $this->requestParams = "?p=linkToken&v_merchant_id=$this->aaaMerchantId&memo=$this->mmmMemo&total=$this->tttTotalCost";
        if(!empty($this->rrrMerchantRef)){
            $this->requestParams .= "&merchant_ref=$this->rrrMerchantRef";
        }
        if($this->cccRecurrentBillingStatus){
            if($this->iiiRecurrenceInterval <= 1){
                throw new InvalidConfigException( 'iiiRecurrenceInterval most be greater than 1 if cccRecurrentBillingStatus is set to true');
            }
            $this->requestParams .= "&recurrent=$this->cccRecurrentBillingStatus&interval=$this->iiiRecurrenceInterval";
        }
        if(!empty($this->nnnNotificationUrl)){
            $this->requestParams .= "&notify_url=$this->nnnNotificationUrl";
        }
        if(!empty($this->sssSuccessUrl)){
            $this->requestParams .= "&success_url=$this->sssSuccessUrl";
        }
        if(!empty($this->fffFailUrl)){
            $this->requestParams .= "&fail_url=$this->fffFailUrl";
        }
        $this->dddDeveloperCode = (empty($this->dddDeveloperCode)) ? '573cedec3bee0' : $this->dddDeveloperCode;
        $this->requestParams .= "&developer_code=$this->dddDeveloperCode";
    }
    
    /**
     * First checks from database if rrrMerchantRef exits and msResponse has not expired. 
     * Sends request to VoguePay for a payment link and saves to database if successful
     * 
     * @return \tecsin\pay2\models\VoguepayMs 
     */
    public function sendRequest()
    {
        //to avoid sending request twice when response has not expired
        if(self::findOne(['rrrMerchantRef' => $this->rrrMerchantRef])){
            if(!$this->hasExpired()){
                return $this;
            }
        }
        // Send the request & save response to $msResponse
        $client = new Client([
            'baseUrl' => 'https://www.voguepay.com',
            ]);
        $response = $client->get($this->requestParams)->send();
        $this->msResponse = $response->content;
        //if a valid url then request was successful
        if((new UrlValidator())->validate($this->msResponse)){
            $this->msExpireAt = strtotime("24 hours");
            $this->save(false);
        }
        return $this;
    }
   
    
    /**
     * 
     * First checks if $msResponse is a valid url, then redirects or send json formated response depending on $showPayButton value.
     * @return mixed Redirects to payment page or send result in json if msResponse is not a valid link or showPayButton is set to true.
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
        if(!$this->showPayButton){
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
     * @param string $ref The rrrMerchantRef code to search database with or the current rrrMerchantRef if not passed as a param.
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
     * Expires within 23hours, 50minutes so as to be 6 minutes lower than the original 
     * 24hours from VoguePay to ensure consistency.
     * @return boolean If the response link ($msResponse) has expired.
     */
    public function hasExpired()
    {
        if($this->msExpireAt < strtotime('23 hours 56 minutes ago')){
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
