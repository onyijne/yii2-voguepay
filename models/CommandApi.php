<?php

##########################################################################
/*
The MIT License (MIT)

Copyright (c) 2014 https://voguepay.com
Edited to Yii2 by Samuel Onyijne <https://www.sajflow.com/samuel>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
##########################################################################

namespace tecsin\pay2\models;

use yii\httpclient\Client;
use yii\helpers\Json;
use yii\httpclient\CurlTransport;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use tecsin\pay2\models\CommandApiHistory;
use Yii;

/**
 * Description of CommandApi
 *
 * @author  https://voguepay.com
 * @author Samuel Onyijne <samuel@sajflow.com>
 */
class CommandApi extends \yii\base\Model {
    
    const EVENT_BEFORE_SEND_COMMAND = 'beforesendcommand';
    const EVENT_AFTER_SEND_COMMAND = 'aftersendcommand';

    protected $baseUrl = 'https://voguepay.com/api/';
    public $ref;
    public $task; 
    public $merchant_id ;
    public $my_username ;
    public $merchant_email_on_voguepay;
    public $command_api_token;
    public $hash;
    public $fetchQty;
    protected $client;
    private $type;


    /**
     *
     * @var mixed A Response object of the client Request sent
     */
    public $cResponse;
    
    /**
     *
     *  OPTIONAL FOR FETCH, accepted keys are
     *  - quantity : (value = int)the numbers of result you want back, default to 10, 
     *  - status : (value = string) the status of transactions that you want, defaults to all
     *  - channel : (value = string) the channel of payment you want in the results
     *  - time : (value = int) the number of minutes transactions have been done that you want
     *  - buyer : (value = string) the email of buyer you want only his/her records
     *  @var array 
     *  @see https://voguepay.com/developers
     */
    public $filterFetchData = [];
    
    /**
     *
     *  REQUIRED FOR SINGLE WITHDRAWAL ONLY. 
     * accepted keys are 
     *  - amount : Required, the amount you are witdrawing 
     *  - bank_name : Required, the bank name you are withdrawing to
     *  - bank_acct_name : the channel of payment you want in the results
     *  - bank_account_number : Required the account number you are witdrawing to
     *  - bank_currency : Optional, eg Nigerian Naira
     *  - bank_country : Optional, eg Nigeria
     *  @var array 
     *  @see https://voguepay.com/developers
     */
    public $filterWithdrawData = [];
    
    /**
     *
     *  REQUIRED FOR MULTIPLE WITHDRAWALS ONLY. 
     * This should be an indexed array where the values are arrays in turn, 
     * whose keys are 
     *  - amount : Required, the amount you are witdrawing 
     *  - bank_name : Required, the bank name you are withdrawing to
     *  - bank_acct_name : the channel of payment you want in the results
     *  - bank_account_number : Required the account number you are witdrawing to
     *  - bank_currency : Optional, eg Nigerian Naira
     *  - bank_country : Optional, eg Nigeria
     * These values would probably be fetched from database
     * example: 
     * ```php
     *     $arrays = Model::findAll(['paymentStatus' => 'isDue']);
     *     foreach($arrays as $key => $array){
     *         $value = [
     *             'amount' => $array['amount'],
     *             'bank_name' => $array['bank_name'],
     *             'bank_acct_name' => $array['bank_acct_name']
     *         ];
     *         if(!empty($array['bank_currency']){
     *             $value['bank_currency'] = $array['bank_currency'];
     *         }
     *         if(!empty($array['bank_country']){
     *             $value['bank_country'] = $array['bank_country'];
     *         }
     *         $this->filterMultipleWithdrawData[][] = $value;      
     *     }
     *     //This would result to
     *         $this->filterMultipleWithdrawData = [
     *             [
     *                 'amount' => '200.50',
     *                 'bank_name' => 'MYBank',
     *                 'bank_acct_name' => 'first last',
     *                 'bank_currency' => 'Nigerian Naira'
     *             ], 
     *             [
     *                 'amount' => '300.50',
     *                 'bank_name' => 'MYOtherBank',
     *                 'bank_acct_name' => 'first last'
     *             ], 
     *         ];
     * ```
     * 
     *  @var array 
     *  @see https://voguepay.com/developers
     */
    public $filterMultipleWithdrawData = [];
    
    /**
     *
     *  REQUIRED FOR SINGLE PAY ONLY, ALL FIELDS ARE REQUIRED, accepted keys are
     *  - amount : the amount to pay, 
     *  - seller :  email of the recipient on VoguePay
     *  - memo :  a description of the payment
     *  @var array 
     *  @see https://voguepay.com/developers
     */
    public $filterPayData = [];
    
     /**
     *
     *  REQUIRED FOR MULTIPLE PAY ONLY, ALL KEYS ARE REQUIRED,
     * This should be an indexed array where the values are arrays in turn, 
     * whose keys are 
     *  - amount : the amount to pay,
     *  - seller : Required, the bank name you are withdrawing to
     *  - memo : the channel of payment you want in the results
     * These values would probably be fetched from database
     * example: 
     * ```php
     *     $arrays = Model::findAll(['paymentStatus' => 'isDue']);
     *     foreach($arrays as $key => $array){
     *         $value = [
     *             'amount' => $array['amount'],
     *             'seller' => $array['seller'],
     *             'memo' => $array['memo']
     *         ];
     *         $this->filterMultiplePayData[][] = $value;      
     *     }
     *     //This would result to
     *         $this->filterMultiplePayData = [
     *             [
     *                 'amount' => '200.50',
     *                 'seller' => 'one@mail.co',
     *                 'memo' => 'pay naration'
     *             ], 
     *             [
     *                 'amount' => '300.50',
     *                 'seller' => 'two@mail.com',
     *                 'memo' => 'first last'
     *             ], 
     *         ];
     * ```
     * 
     *  @var array 
     *  @see https://voguepay.com/developers
     */
    public $filterMultiplePayData = [];
    
    /**
     *
     *  REQUIRED FOR CREATE ONLY
     * accepted keys are 
     *  - username : (REQUIRED) new username for user
     *  - password : (REQUIRED) new password for user
     *  - email : (REQUIRED) user's email
     *  - firstname : (REQUIRED) user's first name
     *  - lastname : (REQUIRED) user's last name
     *  - phone : (REQUIRED) user's mobile number
     *  - referrer : (Optional) your username on voguepay
     *  @var array 
     *  @see https://voguepay.com/developers
     */
    public $filterCreateData = [];
    
    /**
     *
     * @var array The data to pass to postData
     */
    private $data = [];


    public function __construct($config = array()) {
        $this->client = new Client([
            'baseUrl' => $this->baseUrl,
             'requestConfig' => [
                 'format' => Client::FORMAT_JSON
             ],
             'responseConfig' => [
                 'format' => Client::FORMAT_JSON
             ],
            'transport' => CurlTransport::className()
        ]);
        parent::__construct($config);
    }   
    
    public function init() {
        parent::init();
        $this->on('aftersendcommand', [$this, 'notifySystem']);
    }

        public function fetch()
    {
        $this->task = 'fetch';
        $this->setData();
        return $this->sendCommand();        
    }
    
    public function withdraw()
    {  
        $this->task =  'withdraw';
        $this->setData();
        return $this->sendCommand();
    }
    
    /**
     * 
     * @return mixed
     */
    public function withdrawMultiple()
    {  
        $this->task =  'withdraw';        
        $this->setData('multiple');
        return $this->sendCommand();
    }
    
    public function pay()
    {  
        $this->task =  'pay';
        $this->setData();
        return $this->sendCommand();
    }
    
    public function create()
    {  
        $this->task =  'create';
        $this->setData();
        return $this->sendCommand();
    }

    /**
     * sets data to be sent via request
     * 
     * @param string $type
     * @param int $qty
     * @return mixed
     * @throws InvalidConfigException
     */
    private function setData(string $type = 'single')
    {
        $this->ref =  time().mt_rand(0,999999999);
        $this->hash = hash('sha512', $this->command_api_token.$this->task.$this->merchant_email_on_voguepay.$this->ref);
        $this->data = [
            'task' => $this->task,
            'merchant' => $this->merchant_id, 
            'ref' => $this->ref, 
            'hash' => $this->hash, 
        ];
        $this->type = $type;
        if($this->data['task'] == 'fetch'){
            return $this->setFetchDetails();
        }
        if($this->data['task'] == 'withdraw'){
            return $this->setWithdrawalDetails($type);
        }
        if($this->data['task'] == 'pay'){
            return $this->setPayDetails($type);
        }
        if($this->data['task'] == 'create'){
            return $this->setCreateDetails();
        }
        throw new InvalidConfigException('You need to set a valid task.');
    }
    
    private function setFetchDetails() {
        if(empty($this->filterFetchData) || !isset($this->filterFetchData['quantity'])){
            $this->data['quantity'] = 10;
        }
        if(!empty($this->filterFetchData)){
            $this->data =  array_merge($this->data, $this->filterFetchData);
        }
    }
    
    /**
     * 
     * @param string $type single or multiple
     * @return mixed
     * @throws InvalidConfigException
     */
    private function setWithdrawalDetails(string $type) {
        
        //check if multiple
        if($type == 'multiple'){
            if(!ArrayHelper::isIndexed($this->filterMultipleWithdrawData, true) || empty($this->filterMultipleWithdrawData)){
                throw new InvalidConfigException('filterMultipleWithdrawData attribute is most be a consecutive indexed array whose values are arrays, please check the documentation.');
            }
            //loop through every account you want to withdraw into
            $qty = count($this->filterMultipleWithdrawData) ;
            for($i=0; $i<$qty; $i++){
	        $list['id'] = $i+1;               
	        $list['amount'] = $this->filterMultipleWithdrawData[$i]['amount'];
	        $list['bank_name'] = $this->filterMultipleWithdrawData[$i]['bank_name'];
	        $list['bank_acct_name'] = $this->filterMultipleWithdrawData[$i]['bank_acct_name'];
	        $list['bank_account_number'] = $this->filterMultipleWithdrawData[$i]['bank_account_number'];
	        (key_exists('bank_currency', $this->filterMultipleWithdrawData[$i])) ? $list['bank_currency'] = $this->filterMultipleWithdrawData[$i]['bank_currency'] : '';
                (key_exists('bank_country', $this->filterMultipleWithdrawData[$i])) ? $list['bank_country'] = $this->filterMultipleWithdrawData[$i]['bank_country'] : '';
	        $this->data['list'][] = $list;
            }
        } else {
            if(!isset($this->filterWithdrawData['amount']) || !isset($this->filterWithdrawData['bank_name']) || !isset($this->filterWithdrawData['bank_acct_name']) || !isset($this->filterWithdrawData['bank_account_number'])){
                throw new InvalidConfigException('amount, bank_name, bank_acct_name, and bank_account_number are required in $filterWithdrawData[].');
            }
            $this->data = array_merge($this->data, $this->filterWithdrawData);
        }       
        
    }
    
    private function setPayDetails(string $type) {
        if($type == 'single'){
            if(empty($this->filterPayData) || !key_exists('amount', $this->filterPayData) || !key_exists('seller', $this->filterPayData) || !key_exists('memo', $this->filterPayData)){
                throw new InvalidConfigException('amount, seller, and memo as all required as filterPayData property keys. Please see documentation for more.');
            }
            $this->data = array_merge($this->data, $this->filterPayData);
        } else {
            if(!ArrayHelper::isIndexed($this->filterMultiplePayData, true) || empty($this->filterMultiplePayData)){
                throw new InvalidConfigException('filterMultiplePayData attribute is most be a consecutive indexed array whose values are arrays, please check the documentation.');
            }
            $qty = count($this->filterMultiplePayData) ;
            for($i=0; $i<$qty; $i++){
	        $list['id'] = $i+1;               
	        $list['amount'] = $this->filterMultiplePayData[$i]['amount'];
	        $list['seller'] = $this->filterMultiplePayData[$i]['seller'];
	        $list['memo'] = $this->filterMultiplePayData[$i]['memo'];
	        $this->data['list'][] = $list;
            }
        }    
    }
    
    private function setCreateDetails()
    {
        if(!key_exists('referrer', $this->filterCreateData)){
            $this->filterCreateData['referrer'] = 'sajflow';
        }
        if(!ArrayHelper::isSubset($this->filterCreateData, ['username', 'password', 'email', 'firstname', 'lastname', 'phone', 'referrer']))
        {
            throw new InvalidConfigException('required keys are missing as filterCreateData property keys. Please see documentation for more.');
        }
        $this->data = array_merge($this->data, $this->filterCreateData);
    }

    /**
     * @return mixed Sends the command and return it's array response
     */
    protected function sendCommand()
    {
        $this->cResponse = $this->client->setMethod('post')
                                ->setData($this->data)
                                ->setOptions([
                                    CURLOPT_HEADER => false,
                                    CURLOPT_TIMEOUT => 10,
                                    CURLOPT_MAXREDIRS => 2,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_FOLLOWLOCATION => true,               
                                ])->send();
        $this->trigger(self::EVENT_AFTER_SEND_COMMAND);
        if(stripos($this->cResponse->content, '-') || !$this->VerifyResponse() ){
            return Json::encode([
                'error' => [
                    'message' => $this->getResponseError($this->cResponse->content)
                ]
            ]);
        }
        
        return Json::decode($this->cResponse->content);
    }
    
    public function getResponseError($errorCode)
    {
        $messages = [
            '-2' => 'Compromised hash value or Username does not match, probably a wrong data.',
            '-3' => 'Operation failed.',
            'X001' => 'Invalid Merchant ID',
            'X002' => 'Invalid Reference',
            'X003' => 'Invalid hash',
            'X004' => 'Invalid task',
            'X005' => 'Invalid Merchant ID',
            'X006' => 'Invalid hash',
            'C001' => 'Unauthorised access',
            'C002' => 'Invalid Email',
            'C003' => 'Invalid username',
            'C004' => 'Invalid phone number',
            'C005' => 'Invalid firstname',
            'C006' => 'Invalid lastname',
            'C007' => 'Invalid country',
            'C008' => 'Unable to create member',
            'C009' => 'Unable to create member',
            'W001' => 'Invalid amount',
            'W002' => 'Operation Failed.',
            'W003' => 'Amount is below minimum allowed',
            'W004' => 'Insufficient balance',
            'W005' => 'Withdrawal failed',
            'W006' => 'Withdrawal failed',
            'P001' => 'Invalid amount',
            'P002' => 'Operation Failed.',
            'P003' => 'Seller and buyer are one and the same',
            'P004' => 'Invalid beneficiary',
            'P005' => 'Invalid memo',
            'P006' => 'payment amount is below minimum allowed',
            'P007' => 'payment amount exceeds maximum allowed',
            'P008' => 'Insufficient balance for payment',
            'P009' => 'Payment failed',
            'P010' => 'Payment failed',
            'P011' => 'Payment failed',
        ];
        if(key_exists($errorCode, $messages)){
            return $messages[$errorCode];
        } else {
            return 'Unknown error occured.';    
        }
    }
    
    public function getResponse()
    {
        return $this->cResponse->content;
    }
    
    public function VerifyResponse()
    {
        $reply_array = Json::decode($this->cResponse->content);
        //Check that the result is actually from voguepay.com
        $received_hash = $reply_array['hash'];
        $expected_hash = hash('sha512',  $this->command_api_token.  $this->merchant_email_on_voguepay.$reply_array['salt']);
        if($received_hash != $expected_hash || $this->my_username != $reply_array['username']){
	    //Something is wrong. Discard result
            return false;
        } else if(!key_exists('list', $this->data) && $reply_array['status'] != 'OK') {
            //not a multiple request
                //Operation failed
                $this->cResponse->content = '-3';
                return false;
	
        } else {
	    //Operation successful
            return true;
        }
    }
    
    /**
     * do not call this method directly
     * 
     * @return boolean
     */
    public function notifySystem()
    {
        $reply_array = Json::decode($this->cResponse->content);
        if($this->type == 'single'){
            $status = (stripos($this->cResponse->content, '-') || !$this->VerifyResponse() ) ? $this->getResponseError($this->cResponse->content) : $reply_array['description'];
            $model = Yii::createObject(CommandApiHistory::className(), ['ref' => $this->ref, 'task' => $this->task, 'type' => $this->type, 'status' => $status]);
            $model->save();
        } elseif ($this->type == 'multiple') {
            foreach($reply_array['list'] as $list){
		$status = (stripos($this->cResponse->content, '-') || !$this->VerifyResponse() ) ? $this->getResponseError($this->cResponse->content) : $list['description'];
                $model = Yii::createObject(CommandApiHistory::className(), ['ref' => $this->ref, 'task' => $this->task, 'type' => $this->type, 'status' => $status]);
                $model->save();
       	    }
        } else {
            return;
        }
        return true;
    }
}
